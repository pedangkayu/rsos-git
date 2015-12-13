<?php

namespace App\Jobs\Feedback;


use App\Models\data_feedback;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateFeedbackJob extends Job implements SelfHandling {

    public $req, $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $req) {
        $this->req = $req;
        $this->path = public_path() . '/img/feedback/';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //dd($this->req);
        try{

            \DB::begintransaction();

                $parent_id = empty($this->req['parent_id']) ? 0 : $this->req['parent_id'];
                $file = '';
                if(!empty($_FILES['feed_file']['tmp_name'])){
                    $base_name  = date('d_m_Y_') . md5($_FILES['feed_file']['name']) . rand(11111,99999) . time();
                    $fl_name    = $base_name . '.png';
                    $fl = file_get_contents($_FILES['feed_file']['tmp_name']);

                    \Image::make($fl)
                        ->save($this->path . $fl_name);

                    $file = $fl_name;
                }

                $feed = data_feedback::create([
                    'title' => $this->req['feed_title'],
                    'ask' => $this->req['feed_ask'],
                    'link' => $this->req['feed_link'],
                    'parent_id' => $parent_id,
                    'file' => $file,
                    'id_karyawan' => \Me::data()->id_karyawan
                ]);

                $kode = \Format::code($feed->id_feedback);

                $feed->kode = $kode;
                $feed->save();

                if($parent_id > 0){
                    $up = data_feedback::find($parent_id);
                    $up->notif = $up->notif + 1;
                    $up->save();
                }

                \Loguser::create('Megisi form feedback Kode. #' . $kode);

            \DB::commit();

            return [
                'label' => 'success',
                'err' => 'Feedback berhasil terkirim dengan Kode. ' . $feed->kode
            ];

        }catch(\Exception $e){

            \DB::rollback();

            if(file_exists($this->path . $file))
                @unlink($this->path . $file);

            return [
                'label' => 'danger',
                'err' => $e->getMessage()
            ];

        }

    }
}
