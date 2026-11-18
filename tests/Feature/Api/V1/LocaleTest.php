<?php

namespace Tests\Feature\Api\V1;

use App\Enums\TokenAbilityEnum;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::create(['type' => 'admin_language', 'key' => 'admin_language', 'value' => 'ar']);
        Setting::create(['type' => 'site_language', 'key' => 'site_language', 'value' => 'en']);
        settings()->refresh();
    }

    #[Test]
    public function admin_routes_default_to_the_admin_language(): void
    {
        $this->getJson(route('api.v1.admin.cities.index'));

        $this->assertSame('ar', app()->getLocale());
    }

    #[Test]
    public function public_routes_default_to_the_site_language(): void
    {
        $this->getJson(route('api.v1.public.cities.index'));

        $this->assertSame('en', app()->getLocale());
    }

    #[Test]
    public function lang_header_overrides_the_default(): void
    {
        $this->getJson(route('api.v1.public.cities.index'), ['lang' => 'ar']);

        $this->assertSame('ar', app()->getLocale());
    }

    #[Test]
    public function signed_in_user_locale_overrides_the_header(): void
    {
        Sanctum::actingAs(User::factory()->create(['locale' => 'en']), [TokenAbilityEnum::AccessToken->value]);

        $this->getJson(route('api.v1.public.cities.index'), ['lang' => 'ar']);

        $this->assertSame('en', app()->getLocale());
    }
}
