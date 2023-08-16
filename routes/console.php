<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/
Artisan::command('jamboapi:create_super {name} {email} {password}', function(){
    $user = \App\Models\User::create([
        'name' => $this->argument('name'),
        'email' => $this->argument('email'),
        'password' => Hash::make($this->argument('password'),)
    ]);

    $user->assignRole('super_admin');

    $this->info('New super admin created!');
    
})->describe('Create a new super admin account.');

Artisan::command('jamboapi:refresh', function() {
    exec('rm ' . storage_path('logs/laravel*'));
    $this->info('Logs cleared!');

    exec('rm ' . storage_path('framework/sessions/*'));
    $this->info('Session files cleared!');

    Artisan::call('route:clear');
    $this->info('Route cache cleared!');

    Artisan::call('cache:clear');
    $this->info('Application cache cleared!');

    Artisan::call('config:clear');
    $this->info('Configuration cache cleared!');

    Artisan::call('view:clear');
    $this->info('Compiled views cleared!');

})->describe('Clear logs, sessions, route, cache, config and view');