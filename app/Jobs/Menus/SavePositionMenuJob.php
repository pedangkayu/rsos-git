<?php

namespace App\Jobs\Menus;

use App\Models\data_menu;

use App\Jobs\Job;

use Illuminate\Contracts\Bus\SelfHandling;

class SavePositionMenuJob extends Job implements SelfHandling{
    public $req;

    public function __construct(array $req){
        $this->req = $req;
    }
    
    public function handle(){
        $data = json_decode($this->req['update']);
        
        $seri = 1;
        foreach ($data as $menus) {
            
            $menu = data_menu::find($menus->id);
            $menu->parent_id    = 0;
            $menu->seri         = $seri;
            $menu->save();
            $seri++;

            $seri1 = 1;
            if(!empty($menus->children)){
                foreach($menus->children as $child){
                    $menu = data_menu::find($child->id);
                    $menu->parent_id    = $menus->id;
                    $menu->seri         = $seri1;
                    $menu->save();
                    $seri1++;

                    // Children 2
                    $seri2 = 1;
                    if(!empty($child->children)){
                        foreach($child->children as $child2){
                            $menu = data_menu::find($child2->id);
                            $menu->parent_id    = $child->id;
                            $menu->seri         = $seri2;
                            $menu->save();
                            $seri2++;


                            // Children 3
                            $seri3 = 1;
                            if(!empty($child2->children)){
                                foreach($child2->children as $child3){
                                    $menu = data_menu::find($child3->id);
                                    $menu->parent_id    = $child2->id;
                                    $menu->seri         = $seri3;
                                    $menu->save();
                                    $seri3++;
                                    

                                    // Children 4
                                    $seri4 = 1;
                                    if(!empty($child3->children)){
                                        foreach($child3->children as $child4){
                                            $menu = data_menu::find($child4->id);
                                            $menu->parent_id    = $child3->id;
                                            $menu->seri         = $seri4;
                                            $menu->save();
                                            $seri3++;
                                        }
                                    }
                                }

                            }

                        }
                        
                    }
                    
                }
            }
        }

    }
}
