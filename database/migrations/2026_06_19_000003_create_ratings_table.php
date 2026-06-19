<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('producer_id')->constrained('producers')->cascadeOnDelete();
            $table->tinyInteger('stars')->unsigned();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['buyer_id', 'producer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
