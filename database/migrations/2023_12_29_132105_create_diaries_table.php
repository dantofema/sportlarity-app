<?php

use App\Enums\ActivityType;
use App\Enums\FeedingType;
use App\Enums\HydrationType;
use App\Enums\IntensityType;
use App\Enums\PreparationType;
use App\Enums\SleepQualityType;
use App\Enums\SleepTimeType;
use App\Enums\StrengthTrainingType;
use App\Enums\StressType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diaries', function (Blueprint $table): void {
            $table->id();
            $table->dateTime('date');
            $table->foreignId('user_id');
            $table->string('sleep_quality')
                ->default(SleepQualityType::POOR->value);
            $table->string('stress')
                ->default(StressType::LOW->value);
            $table->string('sleep_time')
                ->default(SleepTimeType::MORE_THAN_8->value);
            $table->string('preparation')
                ->default(PreparationType::READY->value);
            $table->string('strength_training')
                ->default(StrengthTrainingType::MODERATE->value);
            $table->string('activity')
                ->default(ActivityType::REST->value);
            $table->string('intensity')
                ->default(IntensityType::REST->value);
            $table->string('feeding')
                ->default(FeedingType::EXCELLENT->value);
            $table->string('hydration')
                ->default(HydrationType::EXCELLENT->value);
            $table->string('weight')
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diaries');
    }
};
