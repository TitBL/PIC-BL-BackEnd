<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['external'],
    'prefix' => 'transaccion',
    'namespace' => 'App\Http\Controllers\External',
], function () {
    // Route::get('/list/{estado}', 'UsuarioController@showAllbyState');
    // Route::get('/buscar/{filtro}', 'UsuarioController@showAllbyFilter');
    // Route::get('/perfil', 'UsuarioController@showPerfil');
    // Route::get('/{id}', 'UsuarioController@show');
    Route::post('/', 'TransaccionController@store');
    // Route::put('/{id}', 'UsuarioController@update');
    // Route::post('/habilitar/{id}', 'UsuarioController@enable');
    // Route::post('/deshabilitar/{id}', 'UsuarioController@disable');
});
