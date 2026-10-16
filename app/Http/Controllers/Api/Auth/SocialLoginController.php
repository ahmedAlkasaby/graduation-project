<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiHomeController;
use App\Models\User;
use App\Models\Device;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialLoginController extends ApiHomeController
{
    public function tokenLogin(Request $request)
    {
        $request->validate([
            'provider'    => 'required|in:google,facebook,apple',
            'token'       => 'required|string', // social token
            'type'        => 'nullable|in:client,delivery,store',
            'imei'        => 'required|string',
            'device_type' => 'nullable|in:android,huawei,apple',
            'firebase_token' => 'required|string', // firebase/expo push token
        ]);

        $provider     = $request->provider;
        $socialToken  = $request->token;
        $imei         = $request->imei;
        $deviceType   = $request->device_type ?? 'android';
        $deviceToken  = $request->firebase_token;

        if ($provider === 'google') {
            $userData = $this->verifyGoogleToken($socialToken);
        } elseif ($provider === 'facebook') {
            $userData = $this->verifyFacebookToken($socialToken);
        } elseif ($provider === 'apple') {
            $userData = $this->verifyAppleToken($socialToken);
        }

        if (!$userData || empty($userData['id'])) {
            return $this->errorResponse(__('auth.invalid_token'), 401);
        }

        // check if user exists
        $user = User::where('provider_id', $userData['id'])
            ->where('provider', $provider)
            ->first();

        $email =  $userData['email'] ?? NULL;
        if ($user) {

            // update info if needed
            $user->update([
                'email'       => $email,
                'name'        => $userData['name'] ?? $email,
                'name_first'  => $userData['name'] ?? $email,
                'name_last'   => $userData['name'] ?? $email,
                // 'provider_token' => $socialToken,
            ]);
        } else {
            $type      = 'client';
            $is_client = 1;



            $user = User::create([
                'email'          => $email,
                'name'           => $userData['name'] ?? $email,
                'name_first'     => $userData['name'] ?? $email,
                'name_last'      => $userData['name'] ?? $email,
                'provider'       => $provider,
                'provider_id'    => $userData['id'],
                // 'provider_token' => $socialToken,
                'active'         => 1,
                'type'           => $type,
                'is_client'      => $is_client,
            ]);
        }

        // update or create device info
        Device::updateDevice($user->id, $imei, $deviceToken, $deviceType);

        // issue API token
        $accessToken = $user->createToken('api-token')->plainTextToken;
        $data = ['access_token' => $accessToken, 'token_type' => 'Bearer'];

        return $this->resourceResponse(new UserResource($user), $data);
    }


    private function verifyGoogleToken($token)
    {
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token={$token}";
        $res = Http::timeout(5)->get($url);
        $email = $res['email']  ?? "";
        return $res->ok() ? [
            'id'    => $res['sub'],
            'email' => $email,
            'name'  => $res['name'] ?? $email,
        ] : null;
    }

    private function verifyFacebookToken($token)
    {
        $url = "https://graph.facebook.com/me?fields=id,name,email&access_token={$token}";
        $res = Http::timeout(5)->get($url);
        $email = $res['email'] ?? "{$res['id']}@facebook.com";
        return $res->ok() ? [
            'id'    => $res['id'], // Facebook user id
            'email' => $email,
            'name'  => $res['name'] ?? $email,
        ] : null;
    }

    private function verifyAppleToken($identityToken)
    {

        $audience = config('services.apple.client_id');

        if (blank($audience)) {
            Log::warning('Apple sign-in attempted with no APPLE_CLIENT_ID configured');

            return null;
        }

        try {

            $appleKeys = Cache::remember(
                'apple:auth-keys',
                now()->addDay(),
                fn () => Http::timeout(5)->retry(2, 200)->get('https://appleid.apple.com/auth/keys')->json()
            );

            $keySet = JWK::parseKeySet($appleKeys);
            $decoded = JWT::decode($identityToken, $keySet);

            if (
                ($decoded->aud ?? null) !== $audience ||
                ($decoded->iss ?? null) !== 'https://appleid.apple.com'
            ) {
                return null;
            }

            return [
                'id' => $decoded->sub,
                'email' => $decoded->email ?? "{$decoded->sub}@apple.com",
                'name' => $decoded->email ?? "{$decoded->sub}@apple.com",
            ];
        } catch (\Exception $e) {

            Cache::forget('apple:auth-keys');

            Log::warning('Apple token verify failed: '.$e->getMessage());

            return null;
        }
    }
}
