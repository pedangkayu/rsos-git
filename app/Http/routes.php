<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/test', function(){
	return bcrypt('admin');
});

Route::get('/', 'HomeController@index');
Route::get('/lock', 'HomeController@lock');

/* 
*	@author : Hexters
*	@access : Guest
*/
Route::controllers([
	'auth' 			=> 'Auth\AuthController',
	'password' 		=> 'Auth\PasswordController',
	'ajax' 			=> 'AjaxController'
]);
/*
*	@access : Auth
*/
Route::group(['middleware' 	=> 'auth'], function () {
	Route::controllers([
		'lockscreen' 		=> 'Auth\LockScreenController',
		'menu' 				=> 'Menus\MenusController',
		'users' 			=> 'Users\UsersController',
		'loguser'			=> 'Users\LogUserController',
		'feedback'			=> 'FeedbackController',

		/* LOGISTIK */
		'logistik' 			=> 'Pengadaan\LogistikController',
		'pmbumum' 			=> 'Pengadaan\PmbUmumController',
		'skb'				=> 'Pengadaan\SKBController',
		'prq'				=> 'Pengadaan\PRQController',
		'stockadj'			=> 'Pengadaan\StockAdjustmentController',
		'subgudang'			=> 'Pengadaan\SubGudangController',
		'returgudang'		=> 'Pengadaan\ReturnGudangController',
		'gudang'			=> 'Pengadaan\MasterGudangController',

		/* PEMBELIAN */
		'sph'				=> 'Pembelian\SPHController',
		'vendor'			=> 'Pembelian\VendorController',
		'po'				=> 'Pembelian\POController',
		'gr'				=> 'Pembelian\SPBMController',
		'returvendor'		=> 'Pembelian\ReturVendorController',

		/* LAPORAN */
		'reportlogistik' 	=> 'Laporan\Logistik\ReportLogistikController',
		'lapsubgudang'		=> 'Laporan\SubGudang\LapranSubGudangController',
		'laporanpo' 		=>  'Laporan\Pembelian\LaporanPOController',

		/* GRAFIKS */
		'grafikpo' 			=> 'Grafiks\GrafikPOController',

		/* AKUTANSI */
		'fakturpembelian' 	=> 'Akutansi\FakturPembelianController'

	]);
});
/* @end Hexters */

/* Yoga */
Route::controllers([
	'karyawan' => 'Personalia\KaryawanController',
	'status_karyawan' => 'Personalia\StatusKaryawanController',
	'recruitment' => 'Personalia\RecruitmentController',
	'employment' => 'Personalia\EmploymentController',
	'penilaian' => 'Personalia\PenilaianKerjaController',
//Refrensi
	'konversi' => 'Refrensi\KonversiController',
	'klasifikasi' => 'Refrensi\KlasifikasiController',
	'satuan' => 'Refrensi\SatuanController',
	'kategori' => 'Refrensi\KategoriController',
	'departemen' => 'Refrensi\DepartemenController',
	'jabatan' => 'Refrensi\JabatanController',
	'coa' => 'Refrensi\MasterCoaController',
	'rekap' => 'Laporan\Transaksi\TransaksiController',

]);

Route::get('/vacancy/list/','Personalia\EmploymentController@getList');