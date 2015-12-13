<?php

namespace App\Http\Controllers\Refrensi;

use Illuminate\Http\Request;

use App\Jobs\RefCoa\CreateCoaJob;
use App\Jobs\RefCoa\CreateCoaLedgerJob;
use App\Jobs\RefCoa\UpdateCoaJob;
use App\Jobs\RefCoa\UpdateCoaLedgerJob;
use App\Jobs\RefCoa\SavePositionCoaJob;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ref_coa;
use App\Models\ref_coa_ledger;

class MasterCoaController extends Controller
{

	public function getIndex(){
		
		$items = ref_coa::with('ledger')
		->get();

		return view('RefCoa.index',[
			'items' => $items
			]);

	}

	public function getLedger(){
		$parent = ref_coa::all();

		return view('RefCoa.ledger',[
			'parent' => $parent,
			]);
	}

	public function getAdd(){

		$menu = ref_coa::all();
		$type = array(
			1 => "Debit",
			2 => "Kredit "
			);

		return view('RefCoa.show', [
			'parent' => $menu,
			'type' => $type,
			]);

	}

	public function postAdd(Request $req){
		$this->dispatch(new CreateCoaJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'COA Berhasil dibuat'
			]);
	}

	public function postLedger(Request $req){
		$this->dispatch(new CreateCoaLedgerJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Coa Ledger Berhasil dibuat'
			]);
	}

	public function getEditgrup($id)
	{
		$data = ref_coa::find($id);
		$menu = ref_coa::all();

		return view('RefCoa.editgrup',[
			'parent' => $menu,
			'data' => $data
			]);
	}

	public function getEditledger($id)
	{
		$data = ref_coa_ledger::find($id);
		$menu = ref_coa::all();

		return view('RefCoa.editledger',[
			'parent' => $menu,
			'data' => $data
			]);
	}

	public function postEditledger(Request $req){
		$this->dispatch(new UpdateCoaLedgerJob($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'COA Ledger berhasil diperbaharui'
			]);
	}

	public function postUpdate(Request $req){
		$this->dispatch(
			new UpdateCoaJob($req->all())
			);

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'COA berhasil diperbaharui'
			]);
	}

	public function postDestroy(Request $req){
		ref_coa::find($req->id)->delete();

		return json_encode([
			'result' => true
			]);
	}

	public function postDestroyledger(Request $req){
		ref_coa_ledger::find($req->id)->delete();

		return json_encode([
			'result' => true
			]);
	}

	
	public function getAllitems(Request $req){
		if($req->ajax()):
			$res = [];

		$items = ref_coa::paginate(10);   

		$out = '';
		if($items->total() > 0){
			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
			foreach($items as $item){

				$out .= '
				<tr class="item_' .  $item->id . ' items">
					<td>' . $no . '</td>
					<td>'. $item->no_coa .'</td>
					<td>'. $item->nm_coa .'</td>
					<td>
						<div>
							' . \Format::indoDate($item->created_at) . '
						</div>
						<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
					</td>
				</tr>
				';
				$no++;
			}
		}else{
			$out = '
			<tr>
				<td colspan="4">Tidak ditemukan</td>
			</tr>
			';
		}

		$res['data'] = $out;
		$res['pagin'] = $items->render();

		return json_encode($res);

		endif;
	}

}
