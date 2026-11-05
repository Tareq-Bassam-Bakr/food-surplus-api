<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['restaurant', 'charity', 'admin'])
                  ->default('restaurant');
            $table->string('city');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['pending', 'active', 'banned'])
                  ->default('pending');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};