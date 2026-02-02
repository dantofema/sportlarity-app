<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('email')->unique()->index();
            $table->string('password');
            $table->string('image')->nullable();
            $table->dateTime('dob')->nullable();
            $table->string('instagram')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_emergency')->nullable();
            $table->decimal('height')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
