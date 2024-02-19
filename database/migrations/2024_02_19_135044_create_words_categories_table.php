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
        Schema::create('words_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('word_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');


            $table->unique(['word_id', 'category_id'], 'word_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('words_categories', function (Blueprint $table) {
            $table->dropForeign(['word_id']);
            $table->dropForeign(['category_id']);
            $table->dropUnique('word_category_unique');
        });

        Schema::dropIfExists('words_categories');
    }
};
