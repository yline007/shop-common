<?php
/**
 * Created by PhpStorm.
 * User: eryue
 * Date: 2019-04-18
 * Time: 11:56
 */

Route::post('/user/register', 'UserController@register');
Route::post('/user/login', 'UserController@login');


Route::group(['middleware' => 'auth:api'], function (){
    Route::get('/user', 'UserController@store');
});