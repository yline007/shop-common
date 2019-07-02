<?php
/**
 * Created by PhpStorm.
 * User: eryue
 * Date: 2019-04-18
 * Time: 11:56
 */



Route::group(['prefix' => 'user'], function (){
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::get('loginout', 'UserController@loginOut');
    Route::get('refresh', 'UserController@refresh');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('/user', 'UserController@store');
});