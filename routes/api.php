<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware(['auth:api']);

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'empresa',
    'namespace' => 'App\Http\Controllers\Api',
], function () {
    Route::get('/list/{estado}', 'EmpresaController@showAllbyState');
    Route::get('/buscar/{filtro}', 'EmpresaController@showAllbyFilter');
    Route::get('/api_key', 'EmpresaController@getApiKEY');
    Route::get('/{id}', 'EmpresaController@show');
    Route::post('/', 'EmpresaController@store');
    Route::put('/{id}', 'EmpresaController@update');
    Route::post('/habilitar/{id}', 'EmpresaController@enable');
    Route::post('/deshabilitar/{id}', 'EmpresaController@disable');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'rol',
    'namespace' => 'App\Http\Controllers\Api',
], function () {
    Route::get('/list/{estado}', 'RolController@showAllbyState');
    Route::get('/buscar/{nombre}', 'RolController@showAllbyName');
    Route::get('/permisos', 'RolController@getListPermission');
    Route::get('/{id}', 'RolController@show');
    Route::post('/', 'RolController@store');
    Route::put('/{id}', 'RolController@update');
    Route::post('/habilitar/{id}', 'RolController@enable');
    Route::post('/deshabilitar/{id}', 'RolController@disable');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'usuario',
    'namespace' => 'App\Http\Controllers\Api',
], function () {
    Route::get('/list/{estado}', 'UsuarioController@showAllbyState');
    Route::get('/buscar/{filtro}', 'UsuarioController@showAllbyFilter');
    Route::get('/perfil', 'UsuarioController@showPerfil');
    Route::get('/{id}', 'UsuarioController@show');
});


Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'documento',
    'namespace' => 'App\Http\Controllers\Api',
], function () {
    Route::get('/visualizar/pdf/{id}', 'DocumentoController@show_pdf');
    Route::get('/visualizar/xml/{id}', 'DocumentoController@show_xml');
    Route::get('/descargar/pdf/{id}', 'DocumentoController@download_pdf');
    Route::get('/descargar/xml/{id}', 'DocumentoController@download_xml');
    Route::get('/list/recibidos/consumidor/', 'DocumentoController@showAllDocByConsumerRecivedFilter');
    Route::get('/list/recibidos/empresa/', 'DocumentoController@showAllDocByCompanyReceivedFilter');
    Route::get('/list/emitidos/empresa/', 'DocumentoController@showAllDocByCompanyIssuerFilter');
});
