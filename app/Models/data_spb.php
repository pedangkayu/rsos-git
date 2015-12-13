<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_spb extends Model{
    
	protected $table 		= 'data_spb';
	protected $primaryKey 	= 'id_spb';
	protected $fillable 	= [
		'no_spb',
		'id_departemen',
		'id_pemohon',
		'id_acc',
		'keterangan',
		'deadline',
		'tipe',
		'tgl_approval',
		'status' /*1:baru|2:belum selesai|3:selesai|4:batal*/
	];

	/*Mengambil data SPB berdasarkan Departement*/
	public function scopeBydepartement($query, $kode, $status = 0, $tujuan = 0, $req = []){
		$spb = $query->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_spb.id_departemen')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spb.id_pemohon')
			->where('data_spb.id_departemen', \Me::data()->id_departemen);
		
		if(!empty($kode))
			$spb->where('data_spb.no_spb', $kode);
		if(!empty($status))
			$spb->where('data_spb.status', $status);
		if(!empty($tujuan))
			$spb->where('data_spb.tipe', $tujuan);
		if(!empty($req['no_verif']) && $req['no_verif'] == 'true')
			$spb->where('data_spb.id_acc', 0);

		else
			$spb->whereIn('data_spb.status', [1,2,3]);

		// $spb->orderby('data_spb.status', 'asc')
		// 	->orderby('data_spb.id_spb', 'asc')
		// 	->orderby('data_spb.id_acc', 'desc')

		$spb->orderby('data_spb.id_spb', 'desc')
			->select('data_spb.*', 'data_departemen.nm_departemen', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');
	}

	/*Mengambil semua data SPB*/
	public function scopeAlldepartement($query, $kode, $status, $departement = 0, $deadline = 0, $surat = 0){

		$akses = \Me::accessGudang();

		$spb = $query->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_spb.id_departemen')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spb.id_pemohon');

		if(!empty($deadline))
			$spb->where('data_spb.deadline', $deadline);
		if(!empty($departement))
			$spb->where('data_spb.id_departemen', $departement);
		if(!empty($kode))
			$spb->where('data_spb.no_spb', $kode);
		if(!empty($status) && !is_array($status))
			$spb->where('data_spb.status', $status);
		else if(is_array($status))
			$spb->whereIn('data_spb.status', $status);
		else
			$spb->whereIn('data_spb.status', [1,2,3]);
		
		if(!empty($surat))
			$spb->where('data_spb.tipe', $surat);
		else
			$spb->whereIn('data_spb.tipe', $akses);

		$spb->orderby('data_spb.status', 'asc')
			->orderby('data_spb.id_spb', 'asc')
			->orderby('data_spb.id_acc', 'desc')
			->select('data_spb.*', 'data_departemen.nm_departemen', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');
	}

	public function scopeRekapspb($query, $req = []){
		$item = $query->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_spb.id_departemen')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spb.id_pemohon');
			
		if(!empty($req['tipe']))
			$item->where('data_barang.tipe', $req['tipe']);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_spb.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_spb.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_spb.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'data_spb.*',
			'data_departemen.nm_departemen',
			'data_karyawan.nm_depan',
			'data_karyawan.nm_belakang'
			);
	}

	public function rekap()
	{
		return $this->hasMany('App\Models\data_spb_item','id_spb');
	}

}