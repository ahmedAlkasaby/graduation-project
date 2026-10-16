<?php

namespace App\Services\Api\Auth;

use App\Enums\SmsTypeEnum;
use App\Exceptions\UserFacingException;
use App\Models\User;
use App\Services\AppSettingsService;
use App\Traits\ResponseMessageTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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

    public function getLocaleInRegister($locale = null): string
    {
        return $locale ?: $this->settings->get('site_language', 'en');
    }

    /**
     * Fill in what registration decides rather than what the app sends: the
     * locale and the fact that a user who got here has already proved they
     * own the address they signed up with.
     *
     * The password is hashed by the model's `hashed` cast.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function getDataRegister(array $input): array
    {
        $input['locale'] = $this->getLocaleInRegister($input['locale'] ?? null);
        $input['email_verified_at'] = new Carbon;

        return $input;
    }

    /**
     * Turn a validated registration into an account.
     *
     * Checking the code, creating the user and burning the code are one
     * transaction: the code is single-use, so a failure between creating the
     * account and consuming it would leave a code that still works and an
     * account that already exists.
     *
     * @param  array<string, mixed>  $input  already validated
     *
     * @throws UserFacingException when the one-time code does not check out
     */
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
    private function consumableCode(array $input)
    {
        $userCode = $this->userCodes->verify(
            (string) ($input['code'] ?? ''),
            (string) $input['type'],
            $input['phone'] ?? null,
            $input['email'] ?? null,
        );

        if (! $userCode) {
            throw new UserFacingException($this->getMessageError('unauthorise'));
        }

        return $userCode;
    }
}
