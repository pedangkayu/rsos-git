<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_feedback extends Model {

	protected $table 		= 'data_feedback';
	protected $primaryKey 	= 'id_feedback';
	protected $fillable 	= [
		'title',
		'ask',
		'link',
		'parent_id',
		'file',
		'status', /* 0:hapus | 1:baru | 2:selesai */
		'id_karyawan',
		'notif'
	];
    
	public function scopeMe($query, $req = []){
		$item = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_feedback.id_karyawan')
			->where('data_feedback.status', '>', 0)
			->where('data_feedback.parent_id', 0);

		if(!in_array(1, \Me::level()))
			$item->where('data_feedback.id_karyawan', \Me::data()->id_karyawan);

		if(!empty($req['nama']))
			$item->where('data_karyawan.nm_depan', 'LIKE', '%' . $req['nama'] . '%');
		if(!empty($req['tanggal']))
			$item->where(\DB::raw('DATE(data_feedback.created_at)'), $req['tanggal']);
		if(!empty($req['status']))
			$item->where('data_feedback.status', $req['status']);
		if(!empty($req['departemen']))
			$item->where('data_karyawan.id_departemen', $req['departemen']);

		$item->select('data_feedback.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang')
			->orderby('data_feedback.status', 'asc');
	}

	public function scopeByid($query, $id){
		$item = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_feedback.id_karyawan')
			->leftJoin('data_departemen', 'data_departemen.id_departemen', '=', 'data_karyawan.id_departemen')		
			->where('data_feedback.status', '>', 0)
			->where('data_feedback.parent_id', 0)
			->where('data_feedback.id_feedback', $id);

		if(!in_array(1, \Me::level()))
			$item->where('data_feedback.id_karyawan', \Me::data()->id_karyawan);

		$item->select(
			'data_feedback.*', 
			'data_karyawan.nm_depan', 
			'data_karyawan.nm_belakang',
			'data_departemen.nm_departemen'
		);
	}

	public function scopeComment($query, $id){
		$item = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_feedback.id_karyawan')
			->where('data_feedback.parent_id', $id)
			->select(
				'data_feedback.*', 
				'data_karyawan.nm_depan', 
				'data_karyawan.nm_belakang'
			)
			->orderby('data_feedback.id_feedback', 'asc');
	}

	public function scopeNotif($query){
		$item = $query->where('status', 1)
			->where('parent_id', 0);

		if(!in_array(1, \Me::level())){
			$item->where('data_feedback.id_karyawan', \Me::data()->id_karyawan);
			$item->select(\DB::raw('SUM(notif) AS total'))
				->groupby('status');
		}else{
			$item->select(\DB::raw('COUNT(notif) AS total'));
		}

	}
}
