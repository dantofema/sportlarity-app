<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content');
            $table->foreignId('user_id');
            $table->foreignId('author_id');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
