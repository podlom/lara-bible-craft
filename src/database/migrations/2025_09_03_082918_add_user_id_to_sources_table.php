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
            // Put next to bibliography_id for clarity
            $table->foreignId('user_id')
                ->nullable()
                ->after('bibliography_id')
                ->constrained()        // defaults to 'users' table and 'id' column
                ->nullOnDelete();      // when user is deleted, keep the source but null the user_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id'); // drops FK and column in one go (Laravel 9+)
        });
    }
};
