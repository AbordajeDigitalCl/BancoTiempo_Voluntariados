<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

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

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user', function () {
    User::create([
        'name' => 'Jose Fonseca',
        'email' => 'myemail@email.com',
        'Apellido_Paterno' => "X",
        'Apellido_Materno' => "X",
        'Age' => "1995-08-10",
        'Telefono' => '2232123454',
        'Direccion' => 'asdfdwaf',
        'Ciudad' => 'Puebla',
        'password' => bcrypt('123456789qq')
    ]);
})->describe('Create sample user');
