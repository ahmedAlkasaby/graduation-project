<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One due amount of an assistance. Its state is read from two timestamps:
 * both NULL = not yet paid, `paid_at` = paid (set later by the payments
 * module), `cancelled_at` = cancelled. Never both, which MySQL enforces with
 * a CHECK constraint; the model enforces it on every driver.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistance_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_assistance_id')->constrained('case_assistances')->restrictOnDelete();
            $table->date('due_at');
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['case_assistance_id', 'due_at'], 'uq_installments_assistance_due');
            $table->index('due_at', 'idx_installments_due_at');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE assistance_installments ADD CONSTRAINT chk_installments_paid_or_cancelled CHECK (paid_at IS NULL OR cancelled_at IS NULL)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assistance_installments');
    }
};
