@extends('Master.Template')

@section('csstop')
<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
<script type="text/javascript" src="{{ asset('/js/refrensi/refrensi.js') }}"></script>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function() { 
		$("#satuan_max").select2(); 
	});

</script>
<style type="text/css">
	.items:hover td .tbl-opsi{
		display: block !important;
	}
</style>
@endsection

@section('title')
	Konversi Satuan Item
@endsection

@section('content')
<div class="row">
	<div class="col-sm-9">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>{{ $items->total() }} Konversi <span class="semi-bold">ditemukan</span></h4>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a> 
					<a href="javascript:getItems(1);" class="reload"></a>
				</div>
			</div>
			<div class="grid-body no-border">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th><a href="javascript:;">Nama Barang</a></th>
							<th><a href="javascript:;">Satuan Max</a></th>
							<th><a href="javascript:;">Satuan Min</a></th>
							<th><a href="javascript:;">Qty</a></th>
							<th>Created At</th>
							<th></th>
						</tr>
					</thead>
					<tbody class="contents-items">
						<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() + $items->currentPage()) -1 ; ?>
						@if($items->total() > 0)
						@forelse($items as $item)
						<tr class="item_{{ $item->id }} items">
							<td>{{ $no }}</td>
							<td>{{ $item->nm_barang }}</td>
							<td>
								{{ $item->satuan_max }}
							</td>
							<td>{{ $item->satuan_min }}</td>
							<td>{{ $item->qty }}</td>
							<td>
								<div>
									{{ Format::indoDate($item->created_at) }}
								</div>
								<small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small>
							</td>
							<td class="text-right">
								@if(Me::data()->id_karyawan != $item->id_karyawan)
								@if(Auth::user()->permission > 2)
								<button type="button" class="close hapus" data-id="{{ $item->id }}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								@endif
								@else

								@endif
							</td>
						</tr>
						<?php $no++; ?>
						@empty

						@endforelse
						@else
						<tr>
							<td colspan="6"><i>Tidak Ada Data, Silakan melakukan penambahan data</i></td>
						</tr>	
						@endif
					</tbody>
				</table>

				<div class="text-right pagins">
					{!! $items->render() !!}
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>Cari <span class="semi-bold">Konversi</span></h4>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a> 
				</div>
			</div>
			<div class="grid-body no-border">
				<p>
					<div class="form-group">
						<div class="form-group">
							<select id="satuan_max" name="src" style="width:100%">
								<option value="">-Pilih-</option>
								@foreach($satuan as $datas)
								<option value="{{ $datas->id_satuan }}"> {{ $datas->nm_satuan }} </option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group">
						<label>Urutan</label>
						<select id="source" style="width:100%" name="orderby">
							<option value="asc">A - Z</option>
							<option value="desc">Z - A</option>
						</select>
					</div>
					<div class="form-group">
						<label>Limit / Page</label>
						<select id="source" style="width:100%" name="limit">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500</option>
						</select>
					</div>

				</p>
				<br />
				<button class="btn btn-block btn-primary cari-barang" type="button"><i class="fa fa-search"></i> Cari</button>
			</div>
		</div>
	</div>
</div>
@endsection