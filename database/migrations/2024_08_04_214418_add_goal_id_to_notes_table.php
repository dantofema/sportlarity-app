<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->foreignId('goal_id')
                ->nullable()
                ->after('author_id')
                ->constrained();
        });
    }

};