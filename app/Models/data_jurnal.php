<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_jurnal extends Model {

    protected $table 		= 'data_jurnal';
	protected $primaryKey 	= 'id_jurnal';
	protected $fillable 	= [
		'id_faktur',
		'id_coa_ledger',
		'tanggal',
		'deskripsi',
		'id_payment_methode',
		'tipe',
		'total'
	];

	public function scopeFaktur($query, $id){
		return $query->join('ref_coa_ledger', 'ref_coa_ledger.id_coa_ledger', '=', 'data_jurnal.id_coa_ledger')
			->where('data_jurnal.id_faktur', $id)
			->where('data_jurnal.tipe', 2)
			->select(
				'data_jurnal.tanggal',
				'ref_coa_ledger.nm_coa_ledger AS akun',
				'data_jurnal.deskripsi',
				'data_jurnal.total'
			);
	}
}
