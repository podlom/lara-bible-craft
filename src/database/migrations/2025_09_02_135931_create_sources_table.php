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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bibliography_id')->constrained()->onDelete('cascade');

            $table->enum('type', ['book', 'article', 'website', 'thesis', 'conference'])->default('book');
            $table->json('authors'); // [{ "last_name": "Шевченко", "initials": "Т. Г." }]
            $table->text('title');
            $table->text('subtitle')->nullable();
            $table->string('responsibility')->nullable(); // редактор, укладач
            $table->string('type_note')->nullable(); // [Текст], [Електронний ресурс]
            $table->string('publisher_city')->nullable();
            $table->string('publisher_name')->nullable();
            $table->string('year', 10)->nullable();
            $table->string('pages', 20)->nullable();
            $table->string('url', 2048)->nullable();
            $table->date('accessed_at')->nullable();

            $table->text('formatted_entry')->nullable(); // згенерований запис
            $table->integer('order_in_list')->nullable(); // позиція у списку
            $table->integer('global_index')->nullable();  // для загального списку
            $table->integer('chapter_index')->nullable(); // для нумерації в главі
            $table->string('chapter_name')->nullable();   // наприклад, "Глава 1"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
