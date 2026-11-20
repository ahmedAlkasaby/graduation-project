<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A beneficiary's profile. Every column is required. The national ID is
 * unique; the phone is indexed but not unique, because family members often
 * share one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charity_cases', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->char('national_id', 14)->unique('uq_charity_cases_national_id');
            $table->string('phone', 30)->index('idx_charity_cases_phone');
            $table->foreignId('category_id')->constrained('case_categories')->restrictOnDelete();
            $table->foreignId('volunteer_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charity_cases');
    }
};
