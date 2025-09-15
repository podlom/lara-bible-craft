<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->enum('type', [
                'book', 'article', 'report', 'law', 'thesis', 'web', 'other',
                'standard', // ⟵ added this
            ])->default('book')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->enum('type', [
                'book', 'article', 'report', 'law', 'thesis', 'web', 'other',
            ])->default('book')->change();
        });
    }
};
