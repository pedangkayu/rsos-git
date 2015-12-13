<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_gudang extends Model{

    protected $table = 'ref_gudang';
	protected $primaryKey = 'id_gudang';
	protected $fillable = [
		'kode_gudang',
		'nm_gudang',
		'status'
	];

	public function scopeListgudang($query, $req =[]){
		$items = $query->where('status', 1);

		if(!empty($req['kode']))
			$items->where('kode_gudang', $req['kode']);
		if(!empty($req['gudang']))
			$items->where('nm_gudang', 'LIKE', '%' . $req['gudang'] . '%');

	}
}
