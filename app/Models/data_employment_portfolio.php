<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_employment_portfolio extends Model
{
    protected $table = 'data_employment_portfolio';
	protected $fillable = [
		'id_employment',
		'company_name',
		'title',
		'location',
		'date_start',
		'date_end',
		'description',

	];
}
