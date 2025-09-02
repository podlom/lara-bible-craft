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
        Schema::create('bibliographies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // власник списку
            $table->string('title');              // Назва списку
            $table->text('description')->nullable(); // Опис (опціонально)
            $table->string('citation_style', 50)->default('dstu'); // Стиль оформлення
            $table->string('language', 10)->default('uk'); // Мова джерел (uk, en)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bibliographies');
    }
};
