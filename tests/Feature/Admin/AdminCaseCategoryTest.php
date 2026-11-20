<?php

namespace Tests\Feature\Admin;

use App\Models\CaseAssistance;
use App\Models\CaseCategory;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCaseCategoryTest extends TestCase
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
    private function signInAdmin(array $permissions): User
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermissions($permissions);
        $this->actingAs($admin, 'web');

        return $admin;
    }

    #[Test]
    public function list_filters_categories_by_status(): void
    {
        CaseCategory::factory()->create(['name' => ['en' => 'Food', 'ar' => 'غذاء']]);
        CaseCategory::factory()->create(['name' => ['en' => 'Housing', 'ar' => 'سكن'], 'active' => 0]);
        $this->signInAdmin(['case_categories.index']);

        $this->get(route('admin.case_categories.index', ['active' => 1]))
            ->assertOk()
            ->assertSee('Food')
            ->assertDontSee('Housing');
    }

    #[Test]
    public function list_requires_the_permission(): void
    {
        $this->signInAdmin(['case_categories.show']);

        $this->get(route('admin.case_categories.index'))->assertRedirect(route('admin.unauthorized'));
    }

    #[Test]
    public function create_edit_and_show_screens_render(): void
    {
        $category = CaseCategory::factory()->create();
        $this->signInAdmin(['case_categories.show', 'case_categories.store', 'case_categories.update']);

        $this->get(route('admin.case_categories.create'))->assertOk();
        $this->get(route('admin.case_categories.edit', $category))->assertOk()->assertSee($category->nameLang('en'));
        $this->get(route('admin.case_categories.show', $category))->assertOk()->assertSee($category->nameLang('en'));
    }

    #[Test]
    public function store_creates_a_category(): void
    {
        $this->signInAdmin(['case_categories.store']);

        $this->post(route('admin.case_categories.store'), [
            'name' => ['en' => 'Orphans', 'ar' => 'أيتام'],
            'order_id' => 6,
            'active' => 1,
        ])->assertRedirect(route('admin.case_categories.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('case_categories', ['name->en' => 'Orphans', 'order_id' => 6]);
    }

    #[Test]
    public function store_validates_the_name(): void
    {
        $this->signInAdmin(['case_categories.store']);

        $this->post(route('admin.case_categories.store'), ['name' => ['en' => 'Orphans'], 'active' => 1])
            ->assertSessionHasErrors('name.ar');
    }

    #[Test]
    public function update_changes_the_category(): void
    {
        $category = CaseCategory::factory()->create();
        $this->signInAdmin(['case_categories.update']);

        $this->put(route('admin.case_categories.update', $category), [
            'name' => ['en' => 'Medical', 'ar' => 'علاج'],
            'active' => 0,
        ])->assertRedirect(route('admin.case_categories.index'))->assertSessionHas('success');

        $this->assertSame('Medical', $category->fresh()->nameLang('en'));
        $this->assertSame(0, (int) $category->fresh()->active);
    }

    #[Test]
    public function status_flips_and_destroy_deletes_an_unused_category(): void
    {
        $category = CaseCategory::factory()->create();
        $this->signInAdmin(['case_categories.status', 'case_categories.destroy']);

        $this->patchJson(route('admin.case_categories.status', $category))->assertOk()->assertJson(['active' => false]);
        $this->delete(route('admin.case_categories.destroy', $category))
            ->assertRedirect(route('admin.case_categories.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted($category);
    }

    #[Test]
    public function destroy_refuses_a_category_that_holds_assistances(): void
    {
        $assistance = CaseAssistance::factory()->create();
        $this->signInAdmin(['case_categories.destroy']);

        $this->delete(route('admin.case_categories.destroy', $assistance->category_id))
            ->assertRedirect(route('admin.case_categories.index'))
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('case_categories', ['id' => $assistance->category_id]);
    }
}
