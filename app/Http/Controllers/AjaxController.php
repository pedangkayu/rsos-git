<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\data_vendor;
use App\Models\data_feedback;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AjaxController extends Controller{
 	public function postStatus(Request $req){
 		if($req->ajax()){
 			User::find(\Auth::user()->id_user)->update([
 				'status_user' => $req->mystatus
 			]);
 		}
 	}

 	public function getFeedback(Request $req){
 		if($req->ajax()){
 			$total = data_feedback::notif()->first();
 			$res = $total->total > 9 ? '9+' : $total->total;
 			return json_encode([
 				'total' => $res
 			]);
 		}
 	} 

 	public function getVendors(Request $req){
 		if($req->ajax()){
 			$res = [];
 			$out = '<option value="">-Pilih Supplier-</option>';
 			$items = data_vendor::where('status', 1)->get();
 			foreach($items as $item){
 				$select = $req->select == $item->id_vendor ? 'selected="selected"' : '';
 				$out .= '<option value="' . $item->id_vendor . '" ' . $select . '>' . $item->nm_vendor . '</option>';
 			}
 			$res['content'] = $out;
 			return json_encode($res);
 		}
 	}
}
