<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_spbm_item extends Model {

    protected $table 		= 'data_spbm_item';
	protected $primaryKey 	= 'id_spbm_item';
	protected $fillable 	= [
		'id_spbm',
		'id_barang',
		'merek',
		'qty_lg',
		'qty',
		'id_satuan',
		'barang_sesuai', /* 0:tida sesuai|1:sesuai */
		'keterangan',
		'bonus',
		'tgl_exp',
		'sisa'
	];

	public function scopeBygr($query, $id){
		$items = $query->join('data_barang', 'data_barang.id_barang', '=', 'data_spbm_item.id_barang')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_spbm_item.id_satuan')
			->join('ref_satuan AS b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_spbm_item.id_spbm', $id)
			->select(
				'data_spbm_item.*',
				'data_barang.nm_barang',
				'data_barang.kode',
				'ref_satuan.nm_satuan',
				'b.nm_satuan AS satuan_default'
			);
	}

}
