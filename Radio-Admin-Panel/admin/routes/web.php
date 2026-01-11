<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RadioStationController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Controllers\RadioStationReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();
Route::get('/', [HomeController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
    Route::get('change-password',[HomeController::class,'change_password_index'])->name('change-password.index');
    Route::post('checkpassword', [HomeController::class, 'checkPassword'])->name('checkpassword');
    Route::post('changepassword', [HomeController::class, 'changePassword'])->name('changepassword');

    Route::resource('city', CityController::class);

    Route::resource('category', CategoryController::class);

    Route::resource('notification', NotificationController::class);

    Route::get('get-category/{city_id}',[RadioStationController::class,'getCategory'])->name('city.category');
    Route::get('get-radio-stations/{city_id}/{category_id}',[RadioStationController::class, 'getRadioStation'])->name('category.radiostation');
    Route::get('get-radio-station-by-category/{category_id}',[RadioStationController::class,'getRadioStationByCategory'])->name('category.radiostations');
    Route::resource('radio-station', RadioStationController::class);

    Route::resource('radio-station-report', RadioStationReportController::class);

    Route::get('setting/about-us', [SettingController::class, 'about_us'])->name('about_us');
    Route::get('setting/privacy-policy', [SettingController::class, 'privacy_policy'])->name('privacy_policy');
    Route::get('setting/terms-condition', [SettingController::class, 'terms_condition'])->name('terms_condition');
    Route::get('setting/system-settings', [SettingController::class, 'system_settings'])->name('system_settings');
    Route::get('setting/notification-settings', [SettingController::class, 'notification_settings'])->name('notification_settings');
    Route::post('setting/update-settings', [SettingController::class ,'update_settings'])->name('update_settings');
    Route::post('setting/update-about-us', [SettingController::class ,'update_about_us'])->name('update_about_us');
    Route::post('setting/update-privacy-policy', [SettingController::class ,'update_privacy_policy'])->name('update_privacy_policy');
    Route::post('setting/update-terms-conditions', [SettingController::class ,'update_terms_condition'])->name('update_terms_condition');
    Route::post('setting/notification-settings', [SettingController::class ,'update_notification_setting'])->name('update_notification_setting');

    Route::post('/slider-change-sequence',[SliderController::class,'changeSequence'])->name('change_squence');
    Route::resource('slider', SliderController::class);

    Route::get('system-update', [SystemUpdateController::class, 'index'])->name('system-update.index');
    Route::post('system-update', [SystemUpdateController::class, 'update'])->name('system-update.update');

    Route::get('setting/admob-settings', [SettingController::class, 'admobIndex'])->name('admob-setting.index');
    Route::post('setting/update-admob-settings',[SettingController::class ,'updateAdmobSetting'])->name('admob-setting.update');
    Route::get('setting/app-settings', [SettingController::class, 'appSettingIndex'])->name('app-setting.index');
    Route::post('setting/update-app-settings',[SettingController::class ,'updateAppSetting'])->name('app-setting.update');

    Route::get('privacy-policy', function () {
        $settings = getSettings('privacy_policy');
        echo $settings['privacy_policy'];
    });

    Route::get('terms-conditions', function(){
        $settings = getSettings('terms_condition');
        echo $settings['terms_condition'];
    });

    Route::get('about-us', function(){
        $settings = getSettings('about_us');
        echo $settings['about_us'];
    });
});


Route::get('clear', function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
});

Route::get('storage-link', function () {
    try {
        Artisan::call('storage:link');
        echo "Storage link created";
    } catch (Exception $e) {
        echo "Storage Link already exists";
    }
});


Route::get('migrate', function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('migrate');
    echo "Migrate Successfully";

});

// Route::get('rollback', function () {
//     Artisan::call('view:clear');
//     Artisan::call('route:clear');
//     Artisan::call('config:clear');
//     Artisan::call('cache:clear');
//     Artisan::call('migrate:rollback');
// });

Route::get('seeder_install', function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('db:seed --class=InstallationSeeder');
    echo "Seeders Installed Successfully";
});
