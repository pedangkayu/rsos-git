@extends('Master.Template')

@section('meta')
<script type="text/javascript" src="{{ asset('/js/recruitment/recruitment.js') }}"></script>
<style type="text/css">
	.items:hover td .tbl-opsi{
		display: block !important;
	}
</style>
@endsection

@section('title')
Admin Recruitment
@endsection

@section('content')
<div class="row">
	<div class="col-sm-9">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>{{ $items->total() }} recruitment <span class="semi-bold">ditemukan</span></h4>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a> 
					<a href="javascript:getItems(1);" class="reload"></a>
				</div>
			</div>
			<div class="grid-body no-border">
				<table class="table table-striped table-flip-scroll cf">
					<thead>
						<tr>
							<th>No</th>
							<th><a href="javascript:;">Jabatan</a></th>
							<th><a href="javascript:;">Tanggal Berlaku</a></th>
							<th><a href="javascript:;">Estimasi Gaji</a></th>
							<th><a href="javascript:;">Status</a></th>
							<th></th>
						</tr>
					</thead>
					<tbody class="contents-items">
						<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() + $items->currentPage()) -1 ; ?>
						@forelse($items as $item)
						<tr class="item_{{ $item->id }} items">
							<td>{{ $no }}</td>
							<td>
								<a href="javascript:;" title="{{ $item->posisi }}" data-toggle="tooltip" data-placement="bottom">{{ $item->posisi }}</a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
										<a href="{{ url('recruitment/detail?id='.$item->id) }}">Lihat</a>
										@if(Auth::user()->permission > 2)
										<!-- hanya admin yang memiliki akses Write dan Execute -->
										| <a href="{{ url('recruitment/update?id='.$item->id) }}">Edit</a> 
										@endif
										]</small>
									</div>
								</td>
								<td>{{ $item->date_open }} s/d {{ $item->date_close }} </td>
								<td>Rp. {{ number_format($item->estimasi_gaji) }}</td>
								<td>
									@if($item->status == 1)
									Aktif
									@else
									Tidak Aktif
									@endif
								</td>
								<td class="text-right">
									@if(Auth::user()->permission > 2)
									<button type="button" class="close hapus" data-nama="{{ $item->posisi }}" data-id="{{ $item->id }}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									@endif
								</td>
							</tr>
							<?php $no++; ?>
							@empty

							@endforelse
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
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<p>
						<a class="btn btn-block btn-primary" href="{{ url('/recruitment/create') }}"><i class="fa fa-plus"></i> Tambah Recruitment</a>
					</p>
					<br />
					<p>
						<h4>Cari <span class="semi-bold">recruitment</span></h4>
						<div class="input-group transparent">
							<span class="input-group-addon ">
								<i class="fa fa-search"></i>
							</span>
							<input type="text" class="form-control" placeholder="Cari recruitment...">
						</div>
						<br />
					</p>

				</div>
			</div>
		</div>
	</div>

	@endsection