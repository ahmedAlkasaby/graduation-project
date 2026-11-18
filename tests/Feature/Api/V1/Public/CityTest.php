<?php

namespace Tests\Feature\Api\V1\Public;

use App\Enums\TokenAbilityEnum;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_lists_only_active_cities(): void
    {
        $active = City::factory()->create();
        City::factory()->inactive()->create();

        $this->getJson(route('api.v1.public.cities.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $active->id);
    }

    #[Test]
    public function admin_without_dashboard_permissions_still_reads_the_public_list(): void
    {
        City::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $this->getJson(route('api.v1.public.cities.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data.data');
    }

    #[Test]
    public function public_context_has_no_write_endpoints_for_cities(): void
    {
        $this->postJson('/api/v1/public/cities', [])->assertMethodNotAllowed();
    }
}
