<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Spatie\Permission\Models\Role;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')->badge(User::where('email', '!=', 'admin@admin.com')->count()),
        ];

        $roles = Role::orderBy('name')
            ->where('name', '!=', 'super_admin')
            ->withCount('users')
            ->get();

        foreach ($roles as $role) {
            $name = $role->name;
            $slug = str($name)->slug()->toString();

            $tabs[$slug] = Tab::make($name)
                ->badge($role->users_count)
                ->modifyQueryUsing(function ($query) use ($role) {
                    return $query->whereHas('roles', fn ($q) => $q->where('name', $role->name));
                });
        }

        return $tabs;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
