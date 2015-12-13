@extends('Master.Template')

@section('meta')
<script type="text/javascript" src="{{ asset('/js/penilaian/penilaian.js') }}"></script>
<style type="text/css">
	.items:hover td .tbl-opsi{
		display: block !important;
	}
</style>
@endsection

@section('title')
Admin Penilaian
@endsection

@section('content')

<div class="row">
	<div class="col-sm-9">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>{{ $items->total() }} Penilaian <span class="semi-bold">ditemukan</span></h4>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a> 
					<a href="javascript:;" class="reload"></a>
				</div>
			</div>
			<div class="grid-body no-border">
				<table class="table table-striped table-flip-scroll cf">
					<thead>
						<tr>
							<th>No</th>
							<th><a href="javascript:;">Nama</a></th>
							<th><a href="javascript:;">NIK</a></th>
							<th><a href="javascript:;">Jabatan</a></th>
							<th><a href="javascript:;">Tgl Penilaian</a></th>
						</tr>
					</thead>
					<tbody>
						<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() + $items->currentPage()) -1 ; ?>
						@if(count($items))
						@forelse($items as $item)
						<tr class="item_{{ $item->id_karyawan }} items">
							<td>{{ $no }}</td>
							<td>
								<a href="javascript:;" title="{{ $item->nm_depan }}" data-toggle="tooltip" data-placement="bottom">{{ $item->nm_depan }} {{ $item->nm_belakang }} </a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
									<!--	<a href="#" data-toggle="modal" data-target="#status" onclick="list({{ $item->id }})" data-nama="{{ $item->nm_depan }}" data-id = "{{ $item->id }}">Lihat</a> -->
												<a href="{{ url('penilaian/list/'.$item->id) }}"> Lihat </a>
										]</small>
								</div>
							</td>
							<td>{{ $item->NIK }}</td>
							<td>{{ $item->nm_jabatan }}</td>
							<td>{{ Format::indoDate(date('y-m-d',strtotime($item->created_at))) }}</td>
							<td class="text-right">
								@if(Auth::user()->permission > 2)
								<button type="button" class="close hapus" data-nama="{{ $item->nm_depan }}" data-id="{{ $item->id_karyawan }}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								@endif
							</td>
						</tr>
						<?php $no++; ?>
						@empty

						@endforelse
						@else
						<tr>
							<td colspan="5"><i>Data Tidak Ada</i></td>
						</tr>
						@endif
					</tbody>
				</table>

				<div class="text-right nav-pagin">
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
					<a class="btn btn-block btn-primary" href="{{ url('/penilaian/section1') }}"><i class="fa fa-plus"></i> Tambah Penilaian</a>
				</p>
				<br />
				<p>
					<h4>Cari <span class="semi-bold">Penilaian</span></h4>
					<div class="input-group transparent">
						<span class="input-group-addon ">
							<i class="fa fa-search"></i>
						</span>
						<input type="text" class="form-control" placeholder="Cari Penilaian...">
					</div>
					<br />
				</p>

			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade bs-example-modal-lg" id="status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<br>
				<i class="icon-credit-card icon-7x"></i>
				
			</div>
				<div class="modal-body detail">
					<i class="fa fa-circle-o-notch fa-spin"></i> Memuat...
				</div>
				<div class="modal-footer">
					<span class="link"></span>
				</div>

		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

@endsection