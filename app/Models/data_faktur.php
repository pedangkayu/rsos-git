<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_faktur extends Model {
    
	protected $table 		= 'data_faktur';
	protected $primaryKey 	= 'id_faktur';
	protected $fillable 	= [
		'nomor_faktur',
		'nomor_type',
		'prefix',
		'type',
		'id_vendor',
		'id_pasien',
		'id_po',
		'tgl_faktur',
		'duodate',
		'id_payment_terms',
		'ppn',
		'diskon',
		'adjustment',
		'subtotal',
		'total',
		'keterangan',
		'status', /* 0:belum bayar|1:nyicil|2:lunas|3:delete */
		'id_karyawan'
	];

	public function scopeViews($query, $id){

		return $query->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_faktur.id_vendor')
			->join('ref_payment_terms', 'ref_payment_terms.id_payment_terms', '=', 'data_faktur.id_payment_terms')
			->whereIn('data_faktur.status', [0,1,2])
			->where('data_faktur.id_faktur', $id)
			->select(
				'data_faktur.*', 
				'data_vendor.kode',
				'data_vendor.nm_vendor', 
				'data_vendor.alamat',
				'data_vendor.telpon',
				'data_vendor.email',
				'ref_payment_terms.payment_terms'
			);


	}

	public function scopeDaftar($query, $req = []){
		$item = $query;
		
		if(!empty($req['no_faktur']))
			$item->where('nomor_faktur', $req['no_faktur']);
		if(!empty($req['tanggal']))
			$item->where('tgl_faktur', $req['tanggal']);
		if(!empty($req['duodate']))
			$item->where('duodate', $req['duodate']);
		if(isset($req['status']) && $req['status'] != '-')
			$item->where('status', $req['status']);
		else
			$item->whereIn('status', [0,1,2]);

	}

}
