<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The support plan for a beneficiary. One-time plans store `requested_amount`,
 * monthly plans store `amount_per_period`; the other stays NULL. There is no
 * status, start or end column: the installments are the schedule's source of
 * truth, and `finished_at` / `cancelled_at` (+ `cancelled_by`) record the
 * plan's closing events.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_assistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('charity_cases')->restrictOnDelete();
            $table->string('assistance_type', 50)->index('idx_assistances_type');
            $table->mediumText('title');
            $table->text('description')->nullable();
            $table->decimal('requested_amount', 12, 2)->nullable();
            $table->decimal('amount_per_period', 12, 2)->nullable();
            $table->tinyInteger('order_id')->default(1);
            $table->tinyInteger('active')->default(1);
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_assistances');
    }
};
