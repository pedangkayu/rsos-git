<?php
	namespace App\Classes\MenuNav;

	use App\Models\data_menu;
	use App\Models\data_menu_user;

	class MenuNav {

		private $path;
	    public function __construct(){
	        $this->path = explode('/', \Request::path());
	    }

	    public function mainMenu(){
        	
	        $menus = data_menu::whereStatus(1)->orderby('seri', 'asc')->get();

	        $permission = [];
	        if(\Auth::check()):
		        foreach (data_menu_user::whereIn('id_level',\Me::level())->get() as $access) {
		            $permission[] = $access->id_menu;
		        }
		    endif;

	        $menu = [];
	        foreach ($menus as $row) {
	            if(in_array($row->id_menu, $permission)){
	            	$menu[$row->parent_id][] = $row;
	            }
	        }

	        return $this->get_menu($menu);
	    }

		private function get_menu($data, $parent = 0, $url = null){
	        static $i = 1;
	        $tab = str_repeat(' ', $i);
	        
	        if(isset($data[$parent])){
	            
	            if($parent == 0){
	                $classUl = ''; # <-- Class dari style / template -> tidak memiliki sub menu
	            }else{
	                $classUl = 'sub-menu'; # <-- Class dari style / template -> pada saat memiliki sub menu
	            }
	            
	            if($url != null && $url != '#'){
	                $link = $url . '/';
	            }else{
	                $link = null;
	            }
	            $activeLi = '';
	            
	            $html = $tab.'<ul class="' . $classUl . '" data-menu="mainmenu">';
	            $i++;
	            
	            foreach($data[$parent] as $v){
	                
	                $l = $link == null ? $v->slug : explode('/', ltrim($link, '/'))[0];

	                $child = $this->get_menu($data, $v->id_menu, $l);
	                
	                if($child){
	                    $liClass = ''; # <- menu aktif
	                    $icon = '<span class="arrow "></span>';
	                    $href = 'javasript:;';
	                    $title = '';
	                    $class = '<i class="' . $v->class . '">';
	                }else{
	                    $liClass = '';
	                    $icon = '';
	                    $href = url() . '/' . $v->slug;
	                    $title = '';
	                    $class = empty($v->class) ? '' : '<i class="' . $v->class . '">';
	                }
	                
	                if($this->path != null){
	                    if(\Request::path() == $v->slug){
	                    	$activeLi = 'start active';
	                    }
	                }
	                $classId = '';
	                if(!empty($v->class_id)){
	                    $classId = 'id="' . $v->class_id . '"';
	                }
	                
	                $html .= $tab.'<li class="' . $activeLi . $liClass . '" ' . $classId . '>';
	                $activeLi = '';
	                $html .= '<a href="' . $href . '" title="' . $v->title . '"> ' . $class . ' </i> <span class="title">' . $v->title . '</span> ' . $icon . '</a>';
	                
	                if($child){
	                    $i--;
	                    $html .= $child;
	                    $html .= $tab;
	                }
	                
	                $html .= '</li>';
	            }
	            
	            $html .= $tab.'</ul>';
	            return $html;
	        
	        }else{
	            
	            return false;
	        
	        }
	        
	    }

	    public function menuPosition(){
	        $menus = data_menu::orderby('seri', 'asc')->get();
	        $menu = [];
	        foreach ($menus as $row) {
	            $menu[$row->parent_id][] = $row;
	        }
	        return $this->formatMenuPosition($menu);
    	}

   		public function formatMenuPosition($data, $parent = 0){
	        static $i = 1;
	        $tab = str_repeat(' ', $i);
	        
	        if(isset($data[$parent])){
	            
	            $html = $tab.'<ol class="dd-list">';
	            $i++;
	            
	            foreach($data[$parent] as $v){
	                
	                $label = $v->status > 0 ? '' : '<span class="label label-danger">Disable</span>';

	                $child = $this->formatMenuPosition($data, $v->id_menu);
	                $html .= $tab.'<li class="dd-item dd3-item" data-id="'.$v->id_menu.'">';
	                $html .= '<div class="dd-handle dd3-handle">
	                            </div>
	                                <div class="dd3-content">
	                                    <i class="' . $v->class . '"></i> 
	                                    <span>'.$v->title.'</span>
	                                    <div class="pull-right">
	                                        ' . $label . '
	                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                                        <a href="' . url('/menu/edit/' . $v->id_menu ) . '" data-toggle="tooltip" data-placement="left" title="Edit Menu"><i class="fa fa-pencil"></i></a>
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

	    // Kases menu user
	    public function MenuAkses($id = 0){
	         $menus = data_menu::orderby('seri', 'asc')
	         	->get();
	        $menu = [];
	        foreach ($menus as $row) {
	            $menu[$row->parent_id][] = $row;
	        }

	        return $this->formatAksesUser($menu, 0, $id);
	    }

	    public function formatAksesUser($data , $parent = 0, $id){
	            
            if(isset($data[$parent])){
               
               $html = '<ul id="tree">';
              
                foreach($data[$parent] as $menu){
                    $child = $this->formatAksesUser($data, $menu['id_menu'], $id);
                    
                    $co = data_menu_user::whereId_level($id)
                    		->whereId_menu($menu['id_menu'])
                    		->count();
                    
                    if($co >= 1){
                        $checked = 'checked="checked"';
                    }else{
                        $checked = '';
                    }
                    
                    $html .= '<li title="'.$menu['ket'].'">';
                    $html .=    '<label>';
                    $html .=        '<input type="checkbox" id="update_akses" '.$checked.' name="id_menu[]" value="'.$menu['id_menu'].'" /> ';
                    $html .=        '<b>'.$menu['title'].'</b> - <small class="text-danger">' . $menu['ket'] . '</small>';
                    $html .=    '</label>';
                    if(($child)){
                        $html .= $child;
                    }
                    $html .= '</li>';
                }
               
               $html .= '</ul>';
               return $html;
                
            }else{
                return false;
            }
            
        }

        public function access(){
	       $paths = '';
	       for($i=0;$i<2;$i++){
	       		if(isset($this->path[$i]))
	       			$paths .= $this->path[$i] . '/';
	       }
	       $path = rtrim($paths, '/');

	        $menu = data_menu_user::whereIn('id_level', \Me::level())->get();
	        $access = [];
	        foreach ($menu as $val) {
	            $access[] = $val->id_menu;
	        }
	        $slug = data_menu::whereIn('id_menu', $access)->select('slug')->get();

	        $slugs = [];
	        foreach ($slug as $aces) {
	            $slugs[] = $aces->slug;
	        }
	        
	        if(!empty(\Request::path())):

	            $cek = empty($path) ? 0 : data_menu::whereSlug($path)->count();
	            if(!empty($path) && !in_array($path, $slugs) && $cek > 0){
	                return [
			        	'return' => false
			        ];
	            }
	            $cek = empty($path) ? 0 : data_menu::whereSlug($path)->count();
	            if(!empty($path) &&  !in_array($path, $slugs) && $cek > 0){
	                return [
			        	'return' => false
			        ];
	            }

	        endif;

	        return [
	        	'return' => true
	        ];
	    }

	}