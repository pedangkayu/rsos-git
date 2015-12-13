<?php

namespace App\Events\Users;

use Image;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UploadAvatarEvent extends Event
{
    use SerializesModels;

    
    private $dir;

    public function __construct(){
        $this->dir   = public_path() . '/img/avatars/';
    }
    
    public function save($file, $crop){

        $dir = $this->dir;

        $base_name  = rand(11111,99999) . time();
        $fl_name    = $base_name . '.png';
        $fl = file_get_contents($file);

        Image::make($file)->crop($crop['w'], $crop['h'], $crop['x'], $crop['y'])->fit(400, 400)->save($dir . 'xl/' . $fl_name);
        Image::make($file)->crop($crop['w'], $crop['h'], $crop['x'], $crop['y'])->fit(300, 300)->save($dir . 'lg/' . $fl_name);
        Image::make($file)->crop($crop['w'], $crop['h'], $crop['x'], $crop['y'])->fit(200, 200)->save($dir . 'md/' . $fl_name);
        Image::make($file)->crop($crop['w'], $crop['h'], $crop['x'], $crop['y'])->fit(100, 100)->save($dir . 'sm/' . $fl_name);
        Image::make($file)->crop($crop['w'], $crop['h'], $crop['x'], $crop['y'])->fit(50,   50)->save($dir . 'xs/' . $fl_name);
        return $fl_name;
    }

    public function rm($name){

        $filexl = $this->dir . 'xl/' . $name;
        $filelg = $this->dir . 'lg/' . $name;
        $filemd = $this->dir . 'md/' . $name;
        $filesm = $this->dir . 'sm/' . $name;
        $filexs = $this->dir . 'xs/' . $name;


        if(file_exists($filexl))
            @unlink($filexl);

        if(file_exists($filelg))
            @unlink($filelg);

        if(file_exists($filemd))
            @unlink($filemd);

        if(file_exists($filesm))
            @unlink($filesm);

        if(file_exists($filexs))
            @unlink($filexs);
        

    }

}
