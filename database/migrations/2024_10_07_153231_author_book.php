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
        Schema::create('author_book',function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('author_id');
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books');
            $table->foreign('author_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
      public function down(): void
    {
        Schema::table('author_book', function (Blueprint $table) {
            $table->dropForeign(['book_id']); // Drop the foreign key constraint
            $table->dropForeign(['author_id']); // Drop the foreign key constraint
        });

        Schema::dropIfExists('author_book');
    }
};
