<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\RelationManagers\DiariesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\NotesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\PlansRelationManager;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {

        /** @var User $record */
        $record = self::getRecord();
        
        return $record->hasRole('wellness')
            ? [
                NotesRelationManager::class,
                PlansRelationManager::class,
                DiariesRelationManager::class
            ]
            : [];
    }
}
