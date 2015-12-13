<?php
namespace App\Classes\TreeCoa;

use App\Models\ref_coa;
use App\Models\data_menu_user;

class CoaNav {

	private $path;
	public function __construct(){
		$this->path = explode('/', \Request::path());
	}

	public function debitPosition(){
		$menus = ref_coa::where('type_coa',1)->orderby('seri', 'asc')->get();
		$menu = [];
		foreach ($menus as $row) {
			$menu[$row->parent_id][] = $row;
		}
		return $this->formatDebitPosition($menu);
	}

	public function kreditPosition(){
		$menus = ref_coa::where('type_coa',2)->orderby('seri', 'asc')->get();
		$menu = [];
		foreach ($menus as $row) {
			$menu[$row->parent_id][] = $row;
		}
		return $this->formatKreditPosition($menu);
	}

	public function formatDebitPosition($data, $parent = 0){
		static $i = 1;
		$tab = str_repeat(' ', $i);
		
		if(isset($data[$parent])){
			
			$html = $tab.'<ol class="dd-list">';
			$i++;
			
			foreach($data[$parent] as $v){
				
				$label = $v->status > 0 ? '' : '<span class="label label-danger">Disable</span> ';

				$child = $this->formatDebitPosition($data, $v->id_coa);
				$html .= $tab.'<li class="dd-item dd3-item" data-id="'.$v->id_coa.'">';
				$html .= '<div class="dd-handle dd3-handle">
			</div>
			<div class="dd3-content">
				<i class="' . $v->class . '"></i> 
				<span>'.$v->no_coa.' - '.$v->nm_coa.'</span>
				<div class="pull-right">
					' . $label . '
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="' . url('/coa/edit/' . $v->id_coa ) . '" data-toggle="tooltip" data-placement="left" title="Edit Menu"><i class="fa fa-pencil"></i></a>
				</div>
				
				';
				
				$html .= '</div>';
				if($child){
					$i--;
					$html .= $child;
					$html .= $tab;
				}
				
				$html .= '</li>';
			}
			$html .= $tab.'</ol>';
			return $html;
			
		}else{
			
			return false;
			
		}
	}

	public function formatKreditPosition($data, $parent = 0){
		static $i = 1;
		$tab = str_repeat(' ', $i);
		
		if(isset($data[$parent])){
			
			$html = $tab.'<ol class="dd-list">';
			$i++;
			
			foreach($data[$parent] as $v){
				
				$label = $v->status > 0 ? '' : '<span class="label label-danger">Disable</span> ';

				$child = $this->formatKreditPosition($data, $v->id_coa);
				$html .= $tab.'<li class="dd-item dd3-item" data-id="'.$v->id_coa.'">';
				$html .= '<div class="dd-handle dd3-handle">
			</div>
			<div class="dd3-content">
				<i class="' . $v->class . '"></i> 
				<span>'.$v->nm_coa.'</span>
				<div class="pull-right">
					' . $label . '
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="' . url('/coa/editkredit/' . $v->id_coa ) . '" data-toggle="tooltip" data-placement="left" title="Edit Menu"><i class="fa fa-pencil"></i></a>
				</div>
				
				';
				
				$html .= '</div>';
				if($child){
					$i--;
					$html .= $child;
					$html .= $tab;
				}
				
				$html .= '</li>';
			}
			$html .= $tab.'</ol>';
			return $html;
			
		}else{
			
			return false;
			
		}
	}
}