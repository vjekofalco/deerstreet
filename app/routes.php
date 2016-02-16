<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
|
*/

Route::get('/{file}',['as' => 'company', 'uses' => 'HomeController@testing']);
Route::post('/{file}', ['uses' => 'HomeController@testing']);

Route::get('/' , ['as'=>'firstLoad', 'uses' => 'HomeController@firstLoad']);

App::missing(function($exception)
{
    return Redirect::route('firstLoad');

});

