<?php

namespace App\Jobs\RefCoa;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\ref_coa;

class SavePositionCoaJob extends Job implements SelfHandling
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
     public $req;

    public function __construct(array $req){
        $this->req = $req;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
     $data = json_decode($this->req['update']);
        //dd($data);

     $seri = 1;
     foreach ($data as $menus) {

      $menu = ref_coa::find($menus->id);
      $menu->parent_id    = 0;
      $menu->seri         = $seri;
      $menu->save();

      $seri1 = 1;
      if(!empty($menus->children)){
        foreach($menus->children as $child){
          $menu = ref_coa::find($child->id);
          $menu->parent_id    = $menus->id;
          $menu->seri         = $seri1;
          $menu->save();
          $seri1++;

                    // Children 2
          $seri2 = 1;
          if(!empty($child->children)){
            foreach($child->children as $child2){
              $menu = ref_coa::find($child2->id);
              $menu->parent_id    = $child->id;
              $menu->seri         = $seri2;
              $menu->save();
              $seri2++;


                            // Children 3
              $seri3 = 1;
              if(!empty($child2->children)){
                foreach($child2->children as $child3){
                  $menu = ref_coa::find($child3->id);
                  $menu->parent_id    = $child2->id;
                  $menu->seri         = $seri3;
                  $menu->save();
                  $seri3++;


                                    // Children 4
                  $seri4 = 1;
                  if(!empty($child3->children)){
                    foreach($child3->children as $child4){
                      $menu = ref_coa::find($child4->id);
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

      $seri++;
    }

  }
}
