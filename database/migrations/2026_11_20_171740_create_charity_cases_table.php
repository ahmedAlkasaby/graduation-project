<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A beneficiary's profile. The national ID is optional but unique when given:
 * a unique index allows any number of NULLs, so cases without one never clash.
 * The phone is indexed for the duplicate warning, not unique, because family
 * members often share one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charity_cases', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->char('national_id', 14)->nullable()->unique('uq_charity_cases_national_id');
            $table->string('phone', 30)->nullable()->index('idx_charity_cases_phone');
            $table->foreignId('category_id')->constrained('case_categories')->restrictOnDelete();
            $table->foreignId('volunteer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charity_cases');
    }
};
