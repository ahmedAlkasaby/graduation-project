<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A beneficiary no longer needs a volunteer to follow them. The foreign key
 * stays, so a volunteer still cannot be deleted while cases point to them.
 *
 * down() fails while a case has no volunteer: those rows have to be assigned
 * one first.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('charity_cases', function (Blueprint $table) {
            $table->dropForeign(['volunteer_id']);
        });

        Schema::table('charity_cases', function (Blueprint $table) {
            $table->unsignedBigInteger('volunteer_id')->nullable()->change();
        });

        Schema::table('charity_cases', function (Blueprint $table) {
            $table->foreign('volunteer_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('charity_cases', function (Blueprint $table) {
            $table->dropForeign(['volunteer_id']);
        });

        Schema::table('charity_cases', function (Blueprint $table) {
            $table->unsignedBigInteger('volunteer_id')->nullable(false)->change();
        });

        Schema::table('charity_cases', function (Blueprint $table) {
            $table->foreign('volunteer_id')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
