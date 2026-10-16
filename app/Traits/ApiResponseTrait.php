<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Exceptions\SmsException;
use App\Exceptions\UserFacingException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;


trait ApiResponseTrait
{
    use AdminHelperTrait, HelperTrait;


    protected function authUser($guard = null)
    {
        if (auth('sanctum')->user()) {
            return auth('sanctum')->user();
        }

        if (auth()->guard($guard)->check()) {
            return auth()->user();
        }

        return null;
    }

    protected function authUserID(): int
    {
        return (int) ($this->authUser()?->id ?? 0);
    }

    /** The caller's audience, as the app understands it. */
    protected function authUserType($guard = null): string
    {
        $user = $this->authUser($guard);

        if (! $user) {
            return '';
        }

        return match (true) {
            (int) $user->is_client === 1 => 'client',
            (int) $user->is_admin === 1 => 'admin',
            default => '',
        };
    }


    // -------------------------------------------------------------------------
    // Responses
    // -------------------------------------------------------------------------

    protected function successResponse($data = null, $message = 'Success', $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'status' => $this->successStatus,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse($message = 'Error', $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => $this->errorStatus,
            'message' => $message,
        ], $statusCode);
    }


    protected function failedResponse(Throwable $e, ?string $fallback = null, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        if ($e instanceof UserFacingException || $e instanceof SmsException) {
            return $this->errorResponse($e->getMessage(), $statusCode);
        }

        $reference = (string) Str::uuid();

        Log::error('Unhandled API exception', [
            'reference' => $reference,
            'exception' => $e::class,
            'message' => $e->getMessage(),
            'path' => request()->path(),
        ]);

        return response()->json([
            'status' => $this->errorStatus,
            'message' => config('app.debug') ? $e->getMessage() : ($fallback ?? __('Server error')),
            'reference' => $reference,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }



    protected function resourceResponse(JsonResource $resource, $data_with = null, $statusCode = Response::HTTP_OK)
    {
        $data = ['data' => $resource];

        foreach ((array) $data_with as $key => $value) {
            $data[$key] = $value;
        }

        return $this->successResponse($data, 'Success', $statusCode);
    }

    /**
     * $data_with adds scalars alongside the collection; $data_with_array adds
     * further named collections (e.g. filters shown next to a list).
     */
    protected function collectionResponse(
        ResourceCollection $collection,
        $data_with = null,
        $data_with_array = null,
        bool $includeMetaLinks = true,
        $statusCode = Response::HTTP_OK
    ) {
        $data = ['data' => $collection->collection];

        if ($includeMetaLinks) {
            $data['meta'] = [
                'total' => $collection->total(),
                'count' => $collection->count(),
                'per_page' => $collection->perPage(),
                'current_page' => $collection->currentPage(),
                'total_pages' => $collection->lastPage(),
                'from' => $collection->firstItem(),
                'last_page' => $collection->lastPage(),
                'path' => $collection->path(),
                'to' => $collection->lastItem(),
            ];

            $data['links'] = [
                'first' => $collection->url(1),
                'last' => $collection->url($collection->lastPage()),
                'prev' => $collection->previousPageUrl(),
                'next' => $collection->nextPageUrl(),
            ];
        }

        foreach ((array) $data_with as $key => $value) {
            $data[$key] = $value;
        }

        foreach ((array) $data_with_array as $key => $value) {
            if (! empty($value)) {
                $data[$key] = $value;
            }
        }

        return $this->successResponse($data, 'Success', $statusCode);
    }

    public function defaultResponse($message = null, $success = true, $data = null): array
    {
        return [
            'status' => $success ? $this->successStatus : $this->errorStatus,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function defaultResponseError($message = null): array
    {
        return ['status' => $this->errorStatus, 'message' => $message];
    }


    protected function getSettingApi(): array
    {
        $setting = settings()->all();
        $keys = array_merge($this->setting_array_api, $this->setting_social_array);

        $out = [];

        foreach ($keys as $key) {
            $out[$key] = $setting[$key] ?? null;
        }

        return $out;
    }
}
