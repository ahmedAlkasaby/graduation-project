<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\DeviceTypeEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{email: string, password: string, imei: string, token: string, device_type: string}
     */
    private function payload(string $login, string $password = 'password'): array
    {
        return [
            'email' => $login,
            'password' => $password,
            'imei' => 'dashboard-browser',
            'token' => 'fcm-token',
            'device_type' => DeviceTypeEnum::Website->value,
        ];
    }

    #[Test]
    public function admin_signs_in_with_email_and_gets_a_token(): void
    {
        $admin = User::factory()->admin()->create();

        $this->postJson(route('api.v1.admin.login'), $this->payload($admin->email))
            ->assertOk()
            ->assertJsonPath('data.data.id', $admin->id)
            ->assertJsonStructure(['data' => ['access_token', 'refresh_token']]);
    }

    #[Test]
    public function super_admin_signs_in_with_phone(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->postJson(route('api.v1.admin.login'), $this->payload($superAdmin->phone))
            ->assertOk()
            ->assertJsonPath('data.data.id', $superAdmin->id);
    }

    #[Test]
    public function app_users_are_rejected(): void
    {
        $donor = User::factory()->create();
        $volunteer = User::factory()->volunteer()->create();

        $this->postJson(route('api.v1.admin.login'), $this->payload($donor->email))->assertUnauthorized();
        $this->postJson(route('api.v1.admin.login'), $this->payload($volunteer->email))->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function inactive_admin_is_rejected(): void
    {
        $admin = User::factory()->admin()->inactive()->create();

        $this->postJson(route('api.v1.admin.login'), $this->payload($admin->email))->assertUnauthorized();
    }

    #[Test]
    public function wrong_password_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->postJson(route('api.v1.admin.login'), $this->payload($admin->email, 'wrong-password'))
            ->assertUnauthorized();
    }

    #[Test]
    public function payload_is_validated(): void
    {
        $this->postJson(route('api.v1.admin.login'), [])
            ->assertUnprocessable();
    }
}
