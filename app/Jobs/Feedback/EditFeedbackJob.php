<?php

namespace App\Jobs\Feedback;

use App\Models\data_feedback;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class EditFeedbackJob extends Job implements SelfHandling {

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
                $file = $this->req['file'];
                if(!empty($_FILES['feed_file']['tmp_name'])){
                    $base_name  = date('d_m_Y_') . md5($_FILES['feed_file']['name']) . rand(11111,99999) . time();
                    $fl_name    = $base_name . '.png';
                    $fl = file_get_contents($_FILES['feed_file']['tmp_name']);

                    \Image::make($fl)
                        ->save($this->path . $fl_name);

                    $file = $fl_name;

                    if(file_exists($this->path . $this->req['file']))
                        @unlink($this->path . $this->req['file']);
                }

                $title = empty($this->req['feed_title']) ? '' : $this->req['feed_title'];
                $link = empty($this->req['feed_link']) ? '' : $this->req['feed_link'];

                $feed = data_feedback::find($this->req['id']);
                $feed->update([
                    'title' => $title,
                    'ask' => $this->req['feed_ask'],
                    'link' => $link,
                    'file' => $file
                ]);

                \Loguser::create('Memperbaharui feedback Kode. ' . $feed->kode);

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
