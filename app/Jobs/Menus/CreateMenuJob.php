<?php

namespace App\Jobs\Menus;

use App\Models\data_menu;

use App\Jobs\Job;

use Illuminate\Contracts\Bus\SelfHandling;

class CreateMenuJob extends Job implements SelfHandling{
   
    public $req;

    public function __construct(array $req){
        $this->req = $req;
    }
    
    public function handle(){
        
        return data_menu::create([
            'parent_id' => $this->req['idparent'],
            'title' => $this->req['title'],
            'slug' => $this->req['slug'],
            'class' => $this->req['class'],
            'class_id' => $this->req['id'],
            'seri' => 0,
            'ket' => $this->req['ket'],
            'status' => $this->req['status']
        ]);
    }
}
