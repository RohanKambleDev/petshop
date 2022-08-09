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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Supports transactions, row-level locking, foreign keys and encryption for tables
            $table->id();
            $table->uuid('uuid')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('is_admin', 1)->default(0);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignUuid('avatar')->nullable()->references('uuid')->on('files')->onDelete('cascade');
            $table->string('address');
            $table->string('phone_number');
            $table->boolean('is_marketing', 1)->default(0);
            $table->timestamps();
            $table->timestamp('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
