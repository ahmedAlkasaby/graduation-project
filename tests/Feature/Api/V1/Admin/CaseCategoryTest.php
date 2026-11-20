<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\CaseAssistance;
use App\Models\CaseCategory;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaseCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);
    }

    /**
     * @param  list<string>  $permissions
     */
    private function actingAsAdmin(array $permissions = []): User
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermissions($permissions);

        Sanctum::actingAs($admin, [TokenAbilityEnum::AccessToken->value]);

        return $admin;
    }

    #[Test]
    public function admin_without_the_permission_is_rejected(): void
    {
        $this->actingAsAdmin(['case_categories.show']);

        $this->getJson(route('api.v1.admin.case_categories.index'))->assertForbidden();
    }

    #[Test]
    public function admin_lists_active_and_inactive_categories(): void
    {
        CaseCategory::factory()->create();
        CaseCategory::factory()->create(['active' => 0]);
        $this->actingAsAdmin(['case_categories.index']);

        $this->getJson(route('api.v1.admin.case_categories.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data.data');
    }

    #[Test]
    public function admin_creates_a_category(): void
    {
        $this->actingAsAdmin(['case_categories.store']);

        $this->postJson(route('api.v1.admin.case_categories.store'), [
            'name' => ['en' => 'Orphans', 'ar' => 'أيتام'],
            'active' => true,
        ])->assertCreated()->assertJsonPath('data.case_category.name', 'Orphans');

        $this->assertDatabaseCount('case_categories', 1);
    }

    #[Test]
    public function creating_a_category_validates_the_payload(): void
    {
        $this->actingAsAdmin(['case_categories.store']);

        $this->postJson(route('api.v1.admin.case_categories.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'active']);
    }

    #[Test]
    public function admin_shows_updates_and_toggles_a_category(): void
    {
        $category = CaseCategory::factory()->create();
        $this->actingAsAdmin(['case_categories.show', 'case_categories.update', 'case_categories.status']);

        $this->getJson(route('api.v1.admin.case_categories.show', $category))
            ->assertOk()
            ->assertJsonPath('data.case_category.id', $category->id)
            ->assertJsonPath('data.case_category.assistances_count', 0);

        $this->putJson(route('api.v1.admin.case_categories.update', $category), [
            'name' => ['en' => 'Medical', 'ar' => 'علاج'],
            'active' => true,
        ])->assertOk()->assertJsonPath('data.case_category.name', 'Medical');

        $this->patchJson(route('api.v1.admin.case_categories.status', $category))
            ->assertOk()
            ->assertJsonPath('data.case_category.active', 0);
    }

    #[Test]
    public function admin_deletes_an_unused_category(): void
    {
        $category = CaseCategory::factory()->create();
        $this->actingAsAdmin(['case_categories.destroy']);

        $this->deleteJson(route('api.v1.admin.case_categories.destroy', $category))->assertOk();

        $this->assertSoftDeleted($category);
    }

    #[Test]
    public function a_category_that_holds_assistances_cannot_be_deleted(): void
    {
        $assistance = CaseAssistance::factory()->create();
        $this->actingAsAdmin(['case_categories.destroy']);

        $this->deleteJson(route('api.v1.admin.case_categories.destroy', $assistance->category_id))->assertConflict();

        $this->assertNotSoftDeleted('case_categories', ['id' => $assistance->category_id]);
    }
}
