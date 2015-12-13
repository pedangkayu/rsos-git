@extends('Master.Template')

@section('meta')
<script type="text/javascript" src="{{ asset('/js/akunting/coa/coa.js') }}"></script>
<style type="text/css">
	.items:hover td .tbl-opsi{
		display: block !important;
	}
</style>
@endsection

@section('title')
Chart Of Account 
@endsection

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="grid simple">
			<div class="grid-title no-border">

				<a href="{{ url('coa/add') }}"><button type="button" class="btn btn-primary">Tambah Group</button></a> 
				<a href="{{ url('coa/ledger') }}"><button type="button" class="btn btn-info">Tambah Ledger</button></a> 

				<div class="tools">
					<a href="javascript:;" class="collapse"></a> 
					<a href="javascript:;" class="reload"></a>
				</div>
			</div>

			<div class="grid-body no-border">
				<table class="table">
					<thead>
						<tr>
							<th colspan="3"><a href="javascript:;">Account Name</a></th>
							<th align=""><a href="javascript:;">Type</a></th>
							<th><a href="javascript:;">Balance</a></th>
							<th ></th>
						</tr>
					</thead>

					<tbody>
						<?php 
						$no = 1;
						$tree = '';
						$head = '';
						$ledger = '';
						?>
						@if(count($items) > 0)
						@foreach($items as $item)
						@if($head != $item->nm_coa && $item->parent_id == 0)
						<tr style="background: #FFFBD4; margin-top:50px;">
							<td colspan="3"><strong>{{ $item->nm_coa }} {{ $item->no_coa }}</strong> </td>
							<td colspan="3">
								<button type="button" class="btn btn-danger btn-xs btn-mini">Group</button>
							</td>
						</tr>
						@endif

						@foreach($items as $row)
						@if($tree != $row->nm_coa && $item->id_coa == $row->parent_id)
						<tr class="items">
							<td width="10"></td>
							<td colspan="2">
								{{ $row->no_coa }} {{ $row->nm_coa }}
							</td>
							<td colspan="2">
								<button type="button" class="btn btn-warning btn-xs btn-mini">Group</button>
							</td>
							<td class="text-right">
								<div style="display:none;" class="tbl-opsi">
									<small>[
									@if(Auth::user()->permission > 2)
									<a href="{{ url('coa/editgrup/'.$row->id_coa) }}">Edit </a> 
									<!-- a href="" class="close hapusCoa" data-id="{{ $row->id_coa }}"> Hapus</a -->
                                    @else
									@endif
									]</small>
								</div>
							</td>
						</tr>

						@foreach($row->ledger as $val)
						@if($ledger != $val->nm_coa_ledger)
						<tr class="items">
							<td></td>
							<td></td>
							<td>
								{{ $val->nm_coa_ledger }}
							</td>
							<td>
							    <button type="button" class="btn btn-success btn-xs btn-mini">Ledger</button>
							</td>
							<td>
								{{ $val->balance }}
							</td>

							<td class="text-right">
								<div style="display:none;" class="tbl-opsi">
									<small>[
									@if(Auth::user()->permission > 2)
									<a href="{{ url('coa/editledger/'.$val->id_coa_ledger) }}">Edit </a> 
									<!-- a href="" class="close hapusCoaLedger" data-id="{{ $val->id_coa_ledger }}"> Hapus</a -->
                                    @else
									@endif
									]</small>
								</div>
							</td>
						</tr>
						@endif
						@endforeach
						@endif

						@endforeach
						@endforeach
						@else
						<tr>
							<td colspan="5"><i>Tidak Ada Data, Silakan melakukan penambahan data</i></td>
						</tr>	
						@endif
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>
@endsection