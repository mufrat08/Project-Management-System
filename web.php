<?php

use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileDeadlineController;
use App\Http\Controllers\GroupAuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TopicMaksController;
use App\Http\Middleware\GroupAuthMiddleware;
use App\Http\Middleware\GroupMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard_old', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard_old');
Route::group(['prefix'=>'student'],function(){
    Route::group(['middleware'=>\App\Http\Middleware\Student::class],function (){
        Route::get('/register','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@register')->name('student.register');
        Route::post('/register','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@storeStudent')->name('student.store');
        Route::get('/login','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@showLoginForm')->name('student.login');
        Route::post('/login','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@login')->name('student.login.submit');

    });
    Route::post('/logout','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@logout')->name('student.logout.submit');

   // Route::post('/store','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@login')->name('student.store');
    Route::group(['middleware'=>\App\Http\Middleware\StudentCheck::class],function (){
       // Route::get('/', 'App\Http\Controllers\StudentController@index')->name('student.dashboard');
        Route::post('/updateInformation', 'App\Http\Controllers\StudentController@updateInformation')->name('student.dashboard.updateInformation');

        Route::resource('/', 'App\Http\Controllers\StudentController', ['names' => 'student.dashboard']);
        Route::post('/join-group', 'App\Http\Controllers\GroupController@join')->name('student.group.join');
        Route::resource('group', 'App\Http\Controllers\GroupController', ['names' => 'student.group']);
       // Route::resource('topic', 'App\Http\Controllers\GroupController', ['names' => 'student.topic']);
        Route::get('/marks', [StudentController::class,'studentMarks'])->name('student.marks');
        Route::resource('topic', 'App\Http\Controllers\TopicController', ['names' => 'student.topic']);
    });


});
Route::get('/clear',function (){
   Artisan::call('route:clear');
   Artisan::call('cache:clear');
   Artisan::call('view:clear');
   Artisan::call('config:clear');
});
Route::get('/mail',[\App\Http\Controllers\DashboardController::class,'mail']);
Route::group(['prefix'=>'teacher'],function(){
    Route::group(['middleware'=>\App\Http\Middleware\Teacher::class],function (){
        Route::get('/register','App\Http\Controllers\TeacherAuth\AuthenticatedSessionController@register')->name('teacher.register');
        Route::post('/register','App\Http\Controllers\TeacherAuth\AuthenticatedSessionController@storeTeacher')->name('teacher.store');
        Route::get('/login','App\Http\Controllers\TeacherAuth\AuthenticatedSessionController@showLoginForm')->name('teacher.login');
        Route::post('/login','App\Http\Controllers\TeacherAuth\AuthenticatedSessionController@login')->name('teacher.login.submit');

    });
    Route::post('/logout','App\Http\Controllers\TeacherAuth\AuthenticatedSessionController@logout')->name('teacher.logout.submit');

    Route::group(['middleware'=>\App\Http\Middleware\TeacherCheck::class],function (){
        Route::resource('/', 'App\Http\Controllers\TeacherController', ['names' => 'teacher.dashboard']);
        Route::resource('/manage-student', 'App\Http\Controllers\ManageStudentController', ['names' => 'teacher.manage-student']);
        Route::resource('/manage-group', 'App\Http\Controllers\TeacherGroupController', ['names' => 'teacher.manage-group']);
        Route::resource('topic', 'App\Http\Controllers\TopicController', ['names' => 'teacher.topic']);
        Route::resource('group', 'App\Http\Controllers\GroupController', ['names' => 'teacher.group']);
        Route::get('topic/assign/{id}',[\App\Http\Controllers\TopicController::class,'assignTopic'])->name('teacher.topic.assign');
        Route::resource('mark', 'App\Http\Controllers\MarkController', ['names' => 'teacher.mark']);
        Route::resource('mark-entry', TopicMaksController::class, ['names' => 'teacher.marks-entry']);
        Route::post('/teacher-email','App\Http\Controllers\TeacherMailController@sendMail')->name('teacher.sendMail');

        Route::get('chat/message/{id}',[\App\Http\Controllers\ChatController::class,'showChat'])->name('teacher.showChat');
        Route::get('/manage-student-delete/{id}',[\App\Http\Controllers\ManageStudentController::class,'delete'])->name('teacher.manage-student.delete');
        Route::resource('chat', 'App\Http\Controllers\ChatController', ['names' => 'teacher.chat']);
        Route::post('/updateInformation', 'App\Http\Controllers\TeacherController@updateInformation')->name('teacher.dashboard.updateInformation');
        Route::resource('/file',FileDeadlineController::class,['names'=>'teacher.file']);
        Route::resource('/deadline',DeadlineController::class,['names'=>'teacher.deadline']);



    });


   // Route::post('/store','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@login')->name('student.store');



});

Route::group(['prefix'=>'group'],function(){
    Route::group(['middleware'=>GroupMiddleware::class],function (){
        Route::get('/register',[GroupAuthController::class,'register'])->name('group.register');
        Route::post('/register',[GroupAuthController::class,'storeTeacher'])->name('group.store');
        Route::get('/login',[GroupAuthController::class,'showLoginForm'])->name('group.login');
        Route::post('/login',[GroupAuthController::class,'login'])->name('group.login.submit');
    });
    Route::post('/logout',[GroupAuthController::class,'logout'])->name('group.logout.submit');


    Route::group(['middleware'=>GroupAuthMiddleware::class],function (){

        Route::get('chat/message/{id}',[\App\Http\Controllers\ChatController::class,'showChat'])->name('group.showChat');

        Route::resource('/', 'App\Http\Controllers\GroupUserController', ['names' => 'group.dashboard']);
        Route::resource('/manage-student', 'App\Http\Controllers\ManageStudentController', ['names' => 'group.manage-student']);
        Route::resource('/manage-group', 'App\Http\Controllers\TeacherGroupController', ['names' => 'group.manage-group']);
        Route::resource('topic', 'App\Http\Controllers\TopicController', ['names' => 'group.topic']);
        Route::resource('group', 'App\Http\Controllers\GroupController', ['names' => 'group.group']);
        Route::resource('chat', 'App\Http\Controllers\ChatController', ['names' => 'group.chat']);
        Route::post('/teacher-email','App\Http\Controllers\TeacherMailController@sendMail')->name('group.sendMail');
        Route::post('/updateInformation', 'App\Http\Controllers\GroupUserController@updateInformation')->name('group.dashboard.updateInformation');
        Route::get('/marks', [\App\Http\Controllers\GroupController::class,'groupMarks'])->name('group.mark');
        Route::resource('/file',FileDeadlineController::class,['names'=>'group.file']);

    });


    // Route::post('/store','App\Http\Controllers\StudentAuth\AuthenticatedSessionController@login')->name('student.store');



});

Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', 'App\Http\Controllers\DashboardController@index')->name('dashboard');
    Route::get('/aa', 'App\Http\Controllers\DashboardController@invoice')->name('dashboard.a');
    Route::resource('roles', 'App\Http\Controllers\RolesController', ['names' => 'dashboard.roles']);
    Route::resource('users', 'App\Http\Controllers\UsersController', ['names' => 'dashboard.users']);
    Route::resource('admins', 'App\Http\Controllers\AdminsController', ['names' => 'dashboard.admins']);

    Route::get('/login', 'App\Http\Controllers\AdminAuth\AuthenticatedSessionController@showLoginForm')->name('dashboard.login');
    Route::post('/login/submit', 'App\Http\Controllers\AdminAuth\AuthenticatedSessionController@login')->name('dashboard.login.submit');

    Route::post('/logout/submit', 'App\Http\Controllers\AdminAuth\AuthenticatedSessionController@logout')->name('dashboard.logout.submit');

    Route::resource('/manage-student', 'App\Http\Controllers\ManageStudentController', ['names' => 'admin.manage-student']);
    Route::resource('/manage-teacher', \App\Http\Controllers\ManageTeacherController::class, ['names' => 'admin.manage-teacher']);
    Route::get('/setting',[\App\Http\Controllers\DashboardController::class,'settings'])->name('dashboard.setting');



    // Route::get('/password/reset', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('dashboard.login');
    // Route::post('/login/submit', 'App\Http\Controllers\Auth\LoginController@login')->name('dashboard.login.submit');
});

require __DIR__ . '/auth.php';
