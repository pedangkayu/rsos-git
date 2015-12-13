<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_prq_item extends Model {

    protected $table 		= 'data_prq_item';
	protected $primaryKey 	= 'id_prq_item';
	protected $fillable 	= [
		'id_prq',
		'id_barang',
		'qty',
		'keterangan',
		'status',
		'id_satuan'
	];

	public function scopeByprq($query, $id){
		return $query->join('data_prq_item', 'data_prq_item.id_barang', '=', 'data_barang.id_barang')
			->where('data_prq_item.id_prq', $id)
			->where('status', 1)
			->select('data_prq_item.*', 'data_barang.nm_barang', 'data_barang.kode');
	}

	public function scopeForsph($query, $req, $status = 1, $ids = []){
		$items = $query->join('data_prq', 'data_prq.id_prq', '=', 'data_prq_item.id_prq')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_prq_item.id_barang')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_prq_item.id_satuan');
		if(is_array($status)){
			$items->whereIn('data_prq.status', $status);
		}else{
			$items->where('data_prq.status', $status);
		}

		if(!empty($req)){
			if(!empty($req['barang']))
				$items->where('data_barang.nm_barang', 'LIKE', '%' . $req['barang'] . '%');
			if(!empty($req['no_prq']))
				$items->where('data_prq.no_prq', $req['no_prq']);
			if(!empty($req['deadline']))
				$items->where('data_prq.target', $req['deadline']);
			if(!empty($req['tanggal']))
				$items->where(\DB::raw('DATE(`data_prq`.`created_at`)'), $req['tanggal']);
		}

		if(!empty($req['vendor']) && $req['titipan'] == 'true')
			$items->where('data_prq.titipan', $req['vendor']);
		else
			$items->where('data_prq.titipan', 0);

		if(count($ids) > 0){
			$items->whereNotIn('id_prq_item', $ids);
		}

		$items->orderby('data_prq_item.status', 'asc')
			->orderby('no_prq', 'asc')
			->select(
				'data_prq.*',
				'data_barang.nm_barang',
				'data_barang.kode',
				'data_prq_item.qty',
				'data_prq_item.id_prq_item',
				'data_prq_item.keterangan',
				'ref_satuan.nm_satuan'
			);

	}
}
