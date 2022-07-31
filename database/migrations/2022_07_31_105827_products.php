<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Supports transactions, row-level locking, foreign keys and encryption for tables
            $table->increments('id');
            $table->foreignUuid('category_uuid')->references('uuid')->on('categories')->onDelete('cascade');
            $table->string('title');
            $table->uuid('uuid');
            $table->double('price', 12, 2);
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
