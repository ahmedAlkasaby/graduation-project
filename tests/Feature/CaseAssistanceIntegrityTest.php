<?php

namespace Tests\Feature;

use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The model-level guards behind the edit restrictions: whatever code path
 * tries it, financial data and installment schedules cannot be rewritten.
 */
class CaseAssistanceIntegrityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_installment_amount_cannot_be_changed(): void
    {
        $installment = AssistanceInstallment::factory()->create(['amount' => 1000]);

        $this->expectException(LogicException::class);

        $installment->update(['amount' => 500]);
    }

    #[Test]
    public function an_installment_due_date_cannot_be_changed(): void
    {
        $installment = AssistanceInstallment::factory()->create(['due_at' => '2027-01-01']);

        try {
            $installment->update(['due_at' => '2027-02-01']);
            $this->fail('The due date was changed.');
        } catch (LogicException) {
            $this->assertSame('2027-01-01', $installment->fresh()->due_at->toDateString());
        }
    }

    #[Test]
    public function an_installment_cannot_be_both_paid_and_cancelled(): void
    {
        $installment = AssistanceInstallment::factory()->create();
        $installment->forceFill(['cancelled_at' => now()])->save();

        $this->expectException(LogicException::class);

        $installment->forceFill(['paid_at' => now()])->save();
    }

    #[Test]
    public function two_installments_of_one_assistance_cannot_share_a_due_date(): void
    {
        $assistance = CaseAssistance::factory()->recurring()->create();
        AssistanceInstallment::factory()->for($assistance, 'assistance')->create(['due_at' => '2027-01-01']);

        $this->expectException(QueryException::class);

        AssistanceInstallment::factory()->for($assistance, 'assistance')->create(['due_at' => '2027-01-01']);
    }

    #[Test]
    public function a_paid_installment_cannot_be_deleted(): void
    {
        $installment = AssistanceInstallment::factory()->paid()->create();

        $this->expectException(LogicException::class);

        $installment->delete();
    }

    #[Test]
    public function the_financial_setup_of_an_assistance_cannot_be_changed(): void
    {
        $assistance = CaseAssistance::factory()->create(['requested_amount' => 5000]);

        foreach ([['requested_amount' => 6000], ['amount_per_period' => 100], ['assistance_type' => 'recurring']] as $change) {
            try {
                $assistance->fresh()->update($change);
                $this->fail('Changed: '.key($change));
            } catch (LogicException) {
                // expected
            }
        }

        $this->assertSame('5000.00', $assistance->fresh()->requested_amount);
    }

    #[Test]
    public function the_title_of_an_assistance_can_still_be_changed(): void
    {
        $assistance = CaseAssistance::factory()->create();

        $assistance->update(['title' => ['en' => 'New', 'ar' => 'جديد'], 'description' => 'Notes']);

        $this->assertSame('New', $assistance->fresh()->titleLang('en'));
    }
}
