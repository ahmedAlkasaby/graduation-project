<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserCodeBuilder extends BaseBuilder
{
    /**
     * The unexpired code issued for a phone or email before the account exists.
     */
    public function latestNotExpired(
        string $type,
        ?string $code = null,
        ?string $phone = null,
        ?string $email = null,
    ): static {
        $this->where('user_id', 0)
            ->where('type', $type)
            ->where('code_expire', '>', now());

        if (! is_null($code)) {
            $this->where('code', $code);
        }

        return $this->where(function (Builder $query) use ($phone, $email) {
            if ($phone) {
                $query->where('phone', $phone);
            }

            if ($email) {
                $phone ? $query->orWhere('email', $email) : $query->where('email', $email);
            }
        });
    }

    /**
     * The unexpired code issued to an existing account, e.g. for a password reset.
     */
    public function pendingForUser(int $userId, string $type): static
    {
        return $this->where('user_id', $userId)
            ->where('type', $type)
            ->where('code_expire', '>', now());
    }
}
