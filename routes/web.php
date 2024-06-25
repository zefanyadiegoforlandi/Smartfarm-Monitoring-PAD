<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DownloadDataController;

use App\Http\Controllers\DaftarFarmerController;
use App\Http\Controllers\DaftarLahanController;
use App\Http\Controllers\DaftarSensorController;
use App\Http\Controllers\DaftarAuthController;

use App\Http\Controllers\DataPertinjauController;
use App\Http\Controllers\DataRainDropController;
use App\Http\Controllers\DataAirQualityController;
use App\Http\Controllers\DataTemperatureController;
use App\Http\Controllers\DataLightController;
use App\Http\Controllers\DataPersentaseKelembapanTanahController;
use App\Http\Controllers\DataHumidityController;
use App\Http\Controllers\DataPressureController;
use App\Http\Controllers\DataApproxAltitudeController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\InformationUserController;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\SessionController;

use Illuminate\Support\Facades\Http;


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

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/pages/dashboard/admin-dashboard', [DashboardController::class, 'index'])
        ->middleware('ensure_admin') // Tambahkan middleware ini untuk mengecek role admin
        ->name('admin-dashboard');

    Route::get('/pages/dashboard/user-dashboard', [DashboardUserController::class, 'index'])
        ->name('user-dashboard');
});

Route::get('/components/table-daftar-lahan', [DashboardController::class, 'table_daftar_lahan'])->name('table-daftar-lahan');
//Daftar Table//

//Daftar Table//
Route::get('/pages/add/daftar-farmer', [DaftarFarmerController::class, 'daftar_farmer'])->name('daftar-farmer');
Route::get('/pages/add/daftar-lahan', [DaftarLahanController::class, 'daftar_lahan'])->name('daftar-lahan');
Route::get('/pages/add/daftar-sensor', [DaftarSensorController::class, 'daftar_sensor'])->name('daftar-sensor');

//Search//
Route::get('/pages/search/search-farmer', [DaftarFarmerController::class, 'search_farmer'])->name('search-farmer');
Route::get('/pages/search/search-lahan', [DaftarLahanController::class, 'search_lahan'])->name('search-lahan');
Route::get('/pages/search/search-sensor', [DaftarSensorController::class, 'search_sensor'])->name('search-sensor');

//Create//
Route::post('/pages/add/daftar-farmer', [DaftarFarmerController::class, 'store_farmer'])->name('farmer-store');
Route::post('/pages/add/daftar-lahan', [DaftarLahanController::class, 'store_lahan'])->name('lahan-store');
Route::post('/pages/add/daftar-sensor', [DaftarSensorController::class, 'store_sensor'])->name('sensor-store');

//Edit-Delete Farmer//
Route::get('/pages/edit-delete/read-farmer/{id}', [DaftarFarmerController::class, 'read_farmer_edit'])->name('read-farmer.edit');
Route::get('/pages/edit-delete/form-farmer/{id}', [DaftarFarmerController::class, 'form_farmer_edit'])->name('form-farmer.edit');
Route::delete('/pages/edit-delete/read-farmer/{id}', [DaftarFarmerController::class, 'read_farmer_destroy'])->name('read-farmer.destroy');
Route::post('/pages/edit-delete/form-farmer/{id}', [DaftarFarmerController::class, 'form_farmer_update'])->name('form-farmer.update');

//Edit-Delete Lahan//
Route::get('/pages/edit-delete/read-lahan/{id}', [DaftarLahanController::class, 'read_lahan_edit'])->name('read-lahan.edit');
Route::get('/pages/edit-delete/form-lahan/{id}', [DaftarLahanController::class, 'form_lahan_edit'])->name('form-lahan.edit');
Route::delete('/pages/edit-delete/read-lahan/{id}', [DaftarLahanController::class, 'read_lahan_destroy'])->name('read-lahan.destroy');
Route::post('/pages/edit-delete/form-lahan/{id}', [DaftarLahanController::class, 'form_lahan_update'])->name('form-lahan.update');

//Edit-Delete Sensor
Route::get('/pages/edit-delete/read-sensor/{id}', [DaftarSensorController::class, 'read_sensor_edit'])->name('read-sensor.edit');
Route::get('/pages/edit-delete/form-sensor/{id}', [DaftarSensorController::class, 'form_sensor_edit'])->name('form-sensor.edit');
Route::delete('/pages/edit-delete/read-sensor/{id}', [DaftarSensorController::class, 'read_sensor_destroy'])->name('read-sensor.destroy');
Route::post('/pages/edit-delete/form-sensor/{id}', [DaftarSensorController::class, 'form_sensor_update'])->name('form-sensor.update');

//Edit-Delete Auth//
Route::get('/pages/edit-delete/read-auth/', [DaftarAuthController::class, 'read_auth_edit'])->name('read-auth.edit');
Route::get('/pages/edit-delete/form-auth/', [DaftarAuthController::class, 'form_auth_edit'])->name('form-auth.edit');
Route::post('/pages/edit-delete/form-auth/{id}', [DaftarAuthController::class, 'form_auth_update'])->name('form-auth.update');

Route::get('/pages/edit-delete/read-user/', [InformationUserController::class, 'read_user_information'])->name('read-user.information');


Route::get('redirects', [UserController::class, 'index']);

Route::get('/user/user-dashboard', [FarmerController::class, 'lihat_dashboard'])->name('dashboard.lihat');
Route::get('/user/pertinjau', [DataPertinjauController::class, 'showPreview'])->name('pertinjau.lihat');

Route::get('/update-data-pertinjau', [DataPertinjauController::class, 'getData'])->name('update-data-pertinjau');

Route::get('/user/download', [DownloadDataController::class, 'index'])->name('download.data');
Route::post('/set-lahan', [SessionController::class, 'setLahan'])->name('set-lahan');
Route::post('/set-sensor', [SessionController::class, 'setSensor'])->name('set-sensor');

Route::get('/user/akun', [FarmerController::class, 'lihat_akun'])->name('akun.lihat');


//Data-Sensor
Route::get('/pages/data-sensor/raindrop', [DataRainDropController::class, 'getData_RainDrop'])->name('raindrop');
Route::get('/update-data-grafik-RainDrop', [DataRainDropController::class, 'updateDataGrafik_RainDrop'])->name('update-data-grafik.Raindrop');
Route::get('/update-data-table-RainDrop', [DataRainDropController::class, 'updateDataTable_RainDrop'])->name('update-data-grafik.Raindrop');

Route::get('/pages/data-sensor/airquality', [DataAirQualityController::class, 'getData_AirQuality'])->name('airquality');
Route::get('/update-data-grafik-AirQuality', [DataAirQualityController::class, 'updateDataGrafik_AirQuality'])->name('update-data-table.AirQuality');
Route::get('/update-data-table-AirQuality', [DataAirQualityController::class, 'updateDataTable_AirQuality'])->name('update-data-table.AirQuality');

Route::get('/pages/data-sensor/temperature', [DataTemperatureController::class, 'getData_Temperature'])->name('temperature');
Route::get('/update-data-grafik-Temperature', [DataTemperatureController::class, 'updateDataGrafik_Temperature'])->name('update-data-grafik.Temperature');
Route::get('/update-data-table-Temperature', [DataTemperatureController::class, 'updateDataTable_Temperature'])->name('update-data-grafik.Temperature');


Route::get('/pages/data-sensor/light', [DataLightController::class, 'getData_Light'])->name('light');
Route::get('/update-data-grafik-Light', [DataLightController::class, 'updateDataGrafik_Light'])->name('update-data-grafik.Light');
Route::get('/update-data-table-Light', [DataLightController::class, 'updateDataTable_Light'])->name('update-data-table.Light');

Route::get('/pages/data-sensor/pressure', [DataPressureController::class, 'getData_Pressure'])->name('pressure');
Route::get('/update-data-grafik-Pressure', [DataPressureController::class, 'updateDataGrafik_Pressure'])->name('update-data-grafik.Pressure');
Route::get('/update-data-table-Pressure', [DataPressureController::class, 'updateDataTable_Pressure'])->name('update-data-table.Pressure');

Route::get('/pages/data-sensor/approxaltitude', [DataApproxAltitudeController::class, 'getData_ApproxAltitude'])->name('approxaltitude');
Route::get('/update-data-grafik-ApproxAltitude', [DataApproxAltitudeController::class, 'updateDataGrafik_ApproxAltitude'])->name('update-data-grafik.ApproxAltitude');
Route::get('/update-data-table-ApproxAltitude', [DataApproxAltitudeController::class, 'updateDataTable_ApproxAltitude'])->name('update-data-table.ApproxAltitude');

Route::get('/pages/data-sensor/persentasekelembapantanah', [DataPersentaseKelembapanTanahController::class, 'getData_PersentaseKelembapanTanah'])->name('persentasekelembapantanah');
Route::get('/update-data-grafik-PersentaseKelembapanTanah', [DataPersentaseKelembapanTanahController::class, 'updateDataGrafik_PersentaseKelembapanTanah'])->name('update-data-grafik.PersentaseKelembapanTanah');
Route::get('/update-data-table-PersentaseKelembapanTanah', [DataPersentaseKelembapanTanahController::class, 'updateDataTable_PersentaseKelembapanTanah'])->name('update-data-table.PersentaseKelembapanTanah');

Route::get('/pages/data-sensor/humidity', [DataHumidityController::class, 'getData_Humidity'])->name('humidity');
Route::get('/update-data-grafik-Humidity', [DataHumidityController::class, 'updateDataGrafik_Humidity'])->name('update-data-grafik.Humidity');
Route::get('/update-data-table-Humidity', [DataHumidityController::class, 'updateDataTable_Humidity'])->name('update-data-table.Humidity');

Route::get('/sensor-data', [DataPertinjauController::class, 'getSensorData']);



 














