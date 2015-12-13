@extends('Master.Template')

@section('meta')
<script type="text/javascript" src="{{ asset('/js/employment/employment.js') }}"></script>
<style type="text/css">
	.items:hover td .tbl-opsi{
		display: block !important;
	}
</style>
@endsection

@section('title')
Admin Data Employment
@endsection

@section('content')
<div class="row">
	<div class="col-sm-9">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>{{ $items->total() }} Employment <span class="semi-bold">ditemukan</span></h4>
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
							<th><a href="javascript:;">Email</a></th>
							<th><a href="javascript:;">Posisi</a></th>
							<th><a href="javascript:;">Tgl Bergabung</a></th>
							<th><a href="javascript:;">Status</a></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() + $items->currentPage()) -1 ; ?>
						@if(count($items) > 0)
						@forelse($items as $item)
						<tr class="item_{{ $item->id}} items">
							<td>{{ $no }}</td>
							<td>
								<a href="javascript:;" title="{{ $item->nm_depan }}" data-toggle="tooltip" data-placement="bottom">{{ $item->nm_depan }} {{ $item->nm_belakang }} </a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
										<a href="{{ url('employment/detail/'.$item->id) }}">Lihat</a>
										@if(Auth::user()->permission > 2)
										<!-- hanya admin yang memiliki akses Write dan Execute -->
										@endif
										]</small>
									</div>
								</td>
								<td>{{ $item->email }}</td>
								<td>{{ $item->posisi }}</td>
								<td>{{ Format::indoDate(date('y-m-d',strtotime($item->created_at))) }}</td>
								<td>
									@if($item->id_status == 1)
										<button type="button" class="btn btn-small btn-primary" data-toggle="modal" data-target="#status" onclick="update({{ $item->id }})" data-nama="{{ $item->nm_depan }}" data-id = "{{ $item->id }}">Pending</button>
									@elseif($item->id_status == 2)
									  <span class="text-info">Diterima</span>
									@else
										<span class="text-danger">Tidak Diterima</span>
									@endif
								</td>
								<td class="text-right">
									@if(Auth::user()->permission > 2)
									<button type="button" class="close hapus" data-nama="{{ $item->nm_depan }}" data-id="{{ $item->id }}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									@endif
								</td>
							</tr>
							<?php $no++; ?>
							@empty

							@endforelse
							@else
							<tr>
								<td colspan="7"><i>Tidak Ada Data Employment</i></td>
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
						<a class="btn btn-block btn-primary" href="{{ url('/vacancy/list') }}"><i class="fa fa-plus"></i> Tambah Employment</a>
					</p>
					<br />
					<p>
						<h4>Cari <span class="semi-bold">employment</span></h4>
						<div class="input-group transparent">
							<span class="input-group-addon ">
								<i class="fa fa-search"></i>
							</span>
							<input type="text" class="form-control" placeholder="Cari Employment...">
						</div>
						<br />
					</p>

				</div>
			</div>
		</div>
	</div>

		<!-- Modal -->
	<div class="modal fade bs-example-modal-sm" id="status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<br>
					<i class="icon-credit-card icon-7x"></i>
					<h3 id="myModalLabel" class="semi-bold posisi"></h3>
				</div>
				<form action="{{ url('employment/update') }}" method="post">
				<div class="modal-body update">
					<i class="fa fa-circle-o-notch fa-spin"></i> Memuat...
				</div>
				<div class="modal-footer">
					<span class="link"></span>
				</div>
				</form>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->

	@endsection