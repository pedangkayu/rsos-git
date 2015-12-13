<?php

namespace App\Jobs\Menus;

use App\Models\data_menu;
use App\Models\data_menu_user;

use App\Jobs\Job;

use Illuminate\Contracts\Bus\SelfHandling;

class UpdateMenuJob extends Job implements SelfHandling{
    public $req;

    public function __construct(array $req){
        $this->req = $req;
    }

    
    public function handle(){
        
        if(!empty($this->req['del'])){
            $id_menu = $this->req['id_menu'];

            $menu = data_menu::find($id_menu);
            $menu->delete();

            data_menu_user::whereId_menu($id_menu)->delete();
        }else{

            $menu = data_menu::find($this->req['id_menu']);

            $menu->title        = $this->req['title'];
            $menu->parent_id    = $this->req['idparent'];
            $menu->slug         = $this->req['slug'];
            $menu->class        = $this->req['class'];
            $menu->status       = $this->req['status'];
            $menu->class_id     = $this->req['id'];
            $menu->ket          = $this->req['ket'];
            $menu->save();

        }
        
    }
}
