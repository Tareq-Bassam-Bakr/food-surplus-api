<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->string('quantity_unit')->default('وجبة');
            $table->timestamp('expiry_time');
            $table->string('image')->nullable();
            $table->string('city');
            $table->enum('status', [
                'available', 'pending', 'approved',
                'rejected', 'collected', 'expired'
            ])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};