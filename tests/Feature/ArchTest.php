<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Wireable;

arch()
    ->expect('App\Enums')
    ->toBeEnums();

arch()
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(Model::class)
    ->ignoring(User::class);

arch()
    ->expect('App\Http')
    ->toOnlyBeUsedIn('App\Http');

arch()->preset()->security()->ignoring('md5');

arch()
    ->expect('App\ValueObjects')
    ->toImplement(Wireable::class);

arch()
    ->expect('App')
    ->toHaveLineCountLessThan(300)
    ->ignoring(['App\Providers']);

arch()->preset()->php();

//arch()->preset()->strict()->ignoring('App\Filament');

//arch()->preset()->laravel();
