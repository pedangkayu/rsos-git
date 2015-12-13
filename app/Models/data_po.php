<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_po extends Model {
    
	protected $table 		= 'data_po';
	protected $primaryKey 	= 'id_po';
	protected $fillable 	= [
		'no_po',
		'id_sph',
		'id_vendor',
		'deadline',
		'id_pembuat',
		'id_acc',
		'status', /* 1:baru|2:proses|3:selesai|4:hapus|5:delete by ststem */
		'adjustment',
		'diskon',
		'ppn',
		'pph',
		'keterangan',
		'titipan'
	];

	public function scopeShow($query, $req = [], $status = 1){

		$po = $query->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_po.id_pembuat');
		if(is_array($status)){
			$po->whereIn('data_po.status', $status);
		}else{
			$po->where('data_po.status', $status);
		}

		if(count($req) > 0){
			if(!empty($req['no_po']))
				$po->where('data_po.no_po', $req['no_po']);
			if(!empty($req['deadline']))
				$po->where('data_po.deadline', $req['deadline']);
			if(!empty($req['titipan']) && $req['titipan'] == 'true')
				$po->where('data_po.titipan', '>', 0);
			if(!empty($req['tanggal']))
				$po->where(\DB::raw('DATE(`data_po`.`created_at`)'), $req['tanggal']);
			if(!empty($req['vendor']))
				$po->where('data_vendor.nm_vendor', 'LIKE', '%' . $req['vendor'] . '%');
		}

		$po->select('data_po.*', 'data_vendor.nm_vendor','data_vendor.telpon', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');

	}

	public function scopeForspbm($query, $req = [], $status = [1,2]){

		$po = $query->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->whereIn('data_po.status', $status);
		if(count($req) > 0){
			if(!empty($req['no_po']))
				$po->where('data_po.no_po', $req['no_po']);
			if(!empty($req['id_vendor']))
				$po->where('data_po.id_vendor', $req['id_vendor']);
			if(!empty($req['tanggal']))
				$po->where(\DB::raw('DATE(data_po.created_at)'), $req['tanggal']);
			if(!empty($req['deadline']))
				$po->where(\DB::raw('DATE(data_po.deadline)'), $req['deadline']);
			if(!empty($req['titipan']) && $req['titipan'] == 'true')
				$po->where('data_po.titipan', '>', 0);
		}
		$po->orderby('data_po.no_po', 'asc')
			->select('data_po.*', 'data_vendor.nm_vendor');
	}

	public function scopeForretur($query, $req = [], $status = [2,3]){

		$po = $query->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->whereIn('data_po.status', $status);
		if(count($req) > 0){
			if(!empty($req['no_po']))
				$po->where('data_po.no_po', $req['no_po']);
			if(!empty($req['id_vendor']))
				$po->where('data_po.id_vendor', $req['id_vendor']);
			if(!empty($req['tanggal']))
				$po->where(\DB::raw('DATE(data_po.created_at)'), $req['tanggal']);
			if(!empty($req['deadline']))
				$po->where(\DB::raw('DATE(data_po.deadline)'), $req['deadline']);
		}
		$po->orderby('data_po.no_po', 'asc')
			->select('data_po.*', 'data_vendor.nm_vendor');
	}

	public function scopeForvendor($query, $id, $req = [], $status = 1){

		$po = $query->where('id_vendor', $id);
		if(is_array($status)){
			$po->whereIn('data_po.status', $status);
		}else{
			$po->where('data_po.status', $status);
		}

		if(count($req) > 0){
			if(!empty($req['no_po']))
				$po->where('data_po.no_po', $req['no_po']);
			if(!empty($req['deadline']))
				$po->where('data_po.deadline', $req['deadline']);
			if(!empty($req['tanggal']))
				$po->where(\DB::raw('DATE(`data_po`.`created_at`)'), $req['tanggal']);
		}

		$po->select('data_po.*');

	}

	public function scopeTop($query, $limit = 5){
		return $query->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->where('data_po.status', 3)
			->where(\DB::raw('YEAR(data_po.created_at)'), date('Y'))
			->select('data_vendor.kode', 'data_vendor.alamat', 'data_vendor.telpon', 'data_vendor.nm_vendor', \DB::raw('COUNT(data_po.id_vendor) AS total'))
			->groupby('data_po.id_vendor')
			->orderby('total', 'asc')
			->limit($limit);
	}

	public function scopeGrafikpembelian($query,$tahun){
		return $query->where(\DB::raw('YEAR(created_at)'), $tahun)
			->where('status', 3)
			->select(\DB::raw('DATE(created_at) AS tanggal, COUNT(id_po) AS total'))
			->groupby(\DB::raw('DATE(created_at)'))
			->orderby(\DB::raw('DATE(created_at)'), 'asc');
	}

	public function scopeActive($query, $req = []){
		$items = $query->whereIn('status', [1,2,3]);
		if(!empty($req['no_po']))
			$items->where('no_po', $req['no_po']);
		if(!empty($req['status']))
			$items->where('status', $req['status']);
	}


	public function scopePoprint($query, $id){
		return $query->leftJoin('data_sph', 'data_sph.id_sph', '=', 'data_po.id_sph')
			->where('data_po.id_po', $id)
			->select(
				'data_po.*',
				'data_sph.no_sph_item AS no_sph'
			);
	}

}
