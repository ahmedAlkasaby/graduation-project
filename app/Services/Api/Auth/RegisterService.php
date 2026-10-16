<?php

namespace App\Services\Api\Auth;

use App\Enums\SmsTypeEnum;
use App\Enums\UserCodeTypeEnum;
use App\Exceptions\UserFacingException;
use App\Models\User;
use App\Models\UserCode;
use App\Notifications\EmailVerification;
use App\Services\AppSettingsService;
use App\Traits\ResponseMessageTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class RegisterService
{
    use ResponseMessageTrait;

    /**
     * Payload keys that describe the registration, not the person.
     *
     * The registration OTP lives in user_codes, never on the user row.
     */
    private const NOT_USER_ATTRIBUTES = [
        'code', 'sms_type', 'confirm_password', 'imei', 'token', 'device_type',
    ];

    public function __construct(
        private AppSettingsService $settings,
        private UserCodeService $userCodes,
    ) {}

    /**
     * Email the code that proves the address belongs to whoever is signing up.
     *
     * An outstanding code is sent again rather than replaced, so an earlier
     * email still holds a working code.
     */
    public function sendVerificationCode(string $email, ?string $phone, int $minutes): void
    {
        $code = $this->userCodes->generate(UserCodeTypeEnum::VerifyRegister, $email, $phone, $minutes);

        Notification::route('mail', $email)->notify(new EmailVerification($code));
    }

    public function getLocaleInRegister($locale = null): string
    {
        return $locale ?: $this->settings->get('site_language', 'en');
    }

    
    public function getDataRegister(array $input): array
    {
        $input['locale'] = $this->getLocaleInRegister($input['locale'] ?? null);
        $input['email_verified_at'] = new Carbon;

        return $input;
    }

    
    public function register(array $input): User
    {
        $input = $this->getDataRegister($input);
        $smsType = SmsTypeEnum::fromRequest($input['sms_type'] ?? null);

        return DB::transaction(function () use ($input, $smsType) {
            // Firebase means the client proved the number before calling us, so
            // there is no code of ours to check.
            $userCode = $smsType->verifiedExternally() ? null : $this->consumableCode($input);

            $user = User::create(Arr::except($input, self::NOT_USER_ATTRIBUTES));

            if ($userCode) {
                $this->userCodes->attachToUser($userCode, $user->id);
            }

            return $user;
        });
    }

    /**
     * The outstanding code for this registration, or a failure the caller can
     * show the user.
     *
     * @param  array<string, mixed>  $input
     */
    private function consumableCode(array $input): UserCode
    {
        $userCode = $this->userCodes->verify(
            code: (string) ($input['code'] ?? ''),
            type: UserCodeTypeEnum::VerifyRegister,
            email: $input['email'],
        );

        if (! $userCode) {
            throw new UserFacingException($this->getMessageError('unauthorise'));
        }

        return $userCode;
    }
}
