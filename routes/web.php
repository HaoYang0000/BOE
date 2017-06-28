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

//主页
Route::get('/', 'ChartController@readExcel');

//上传文件
Route::post('uploadExcel', 'ChartController@uploadExcel');
Route::get('uploadExcel', function (){
	return view('charts.uploadChart');
});

//从表格更新数据库数据
Route::get('/updateExcel/{file}','ChartController@updateExcelData');
//从数据库删除数据
Route::get('/deleteForm/{file}', 'ChartController@deleteExcelData');
//从服务器上删除图表
Route::get('/deleteFile/{file}','ChartController@deleteFile');
//下载表格
Route::get('/downloadForm/{file}', 'ChartController@downloadExcel');
//生成线上数据图
Route::get('/showChart/{file}', 'ChartController@showBarChart');
//生成可下载Excel图表
Route::get('/generateChart/{file}', 'ChartController@generateExcelChart');

//Table testing
Route::get('/table/{file}', 'ChartController@showDataTable');

//Excel testing
// Route::get('excel/export','ChartController@export');
// Route::post('/send', 'DataController@store');
// Route::get('/bar', 'ChartController@showBarChart');
