<?php

namespace App\Jobs\Users;

use App\User;
use App\Models\data_level;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class editUserJob extends Job implements SelfHandling
{
    
    public $req;

    public function __construct(array $req){
        $this->req = $req;
    }

    
    public function handle(){

        $user = User::find($this->req['id_user']);
        $user->permission = $this->req['permission'];
        if($this->req['username'] != $this->req['first_username'])
            $user->username = $this->req['username'];
        if(!empty($this->req['password']))
            $user->password = bcrypt($this->req['password']);
        $user->save();

        data_level::whereId_user($this->req['id_user'])->delete();
        foreach($this->req['levels'] as $level){
            data_level::firstOrCreate([
                'id_user' => $user->id_user,
                'id_level_user' => $level
            ]);
        }
        return $this->req;

    }
}
