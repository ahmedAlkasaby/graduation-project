<?php

namespace App\Http\Resources;

use App\Traits\AdminHelperTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Base for every API resource: resolves the language a translatable column
 * should be read in, and who is asking.
 */
class MainResource extends JsonResource
{
    use AdminHelperTrait;

    /**
     * A signed in user's own locale wins over the Accept-Language style header,
     * so the app shows what the account is set to.
     */
    protected function getLang($language = 'en'): string
    {
        $lang = auth('sanctum')->check()
            ? auth('sanctum')->user()->locale
            : $language;

        return in_array($lang, $this->language_array) ? $lang : $this->default_language_use;
    }

    /** The language for this request, taken from the `lang` header. */
    protected function lang(): string
    {
        return $this->getLang(request()->header('lang') ?? $this->default_language_use);
    }

    /** One value out of a translatable json column. */
    protected function translated($value): ?string
    {
        if (is_array($value)) {
            return $value[$this->lang()] ?? reset($value) ?: null;
        }

        return $value;
    }

    protected function getAuthUser()
    {
        return auth('sanctum')->check() ? auth('sanctum')->user() : null;
    }

    protected function getAuthUserID(): int
    {
        return (int) ($this->getAuthUser()?->id ?? 0);
    }
}
