<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/init', "ScheduleController@init");
Route::get('/schedule', "ScheduleController@index");
Route::get('/merge', "ScheduleController@merge");
Route::get('/detail', "ScheduleController@detail");
Route::get('/make-classroom', "ScheduleController@makeClassroom");
Route::get('/set-teacher', "ScheduleController@setTeacher");
Route::get('/teacher-subject', "ScheduleController@teacherSubjects");
Route::post('/submit-alpha', "ScheduleController@submitAlpha")->name('submit-alpha');
