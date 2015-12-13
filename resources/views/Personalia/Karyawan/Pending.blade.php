@extends('Master.Template')

@section('meta')
<script type="text/javascript" src="{{ asset('/js/personalia/karyawan.js') }}"></script>
<style type="text/css">
	.items:hover td .tbl-opsi{
		display: block !important;
	}
</style>
@endsection

@section('title')
Karyawan Pending
@endsection

@section('content')
<div class="row">
	<div class="col-sm-9">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>{{ $items->total() }} Karyawan Pending <span class="semi-bold">ditemukan</span></h4>
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
							<th><a href="javascript:;">Tgl Bergabung</a></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() + $items->currentPage()) -1 ; ?>
						@forelse($items as $item)
						<tr class="item_{{ $item->id_karyawan }} items">
							<td>{{ $no }}</td>
							<td>
								<a href="javascript:;" title="{{ $item->nm_depan }}" data-toggle="tooltip" data-placement="bottom">{{ $item->nm_depan }} {{ $item->nm_belakang }} </a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
										<a href="{{ url('status_karyawan/aktif/'.$item->id_karyawan) }}">Aktifkan</a>
										]</small>
									</div>
								</td>
								<td>{{ $item->NIK }}</td>
								<td>{{ Format::indoDate(date('y-m-d',strtotime($item->tgl_bergabung))) }}</td>
								<td class="text-right">
									@if(Me::data()->id_karyawan != $item->id_karyawan)
									@if(Auth::user()->permission > 2)
									<button type="button" class="close hapus" data-nama="{{ $item->nm_depan }}" data-id="{{ $item->id_karyawan }}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									@endif
									@else

									@endif
								</td>
							</tr>
							<?php $no++; ?>
							@empty

							@endforelse
						</tbody>
					</table>

					<div class="text-right nav-pagin">
						{!! $items->render() !!}
					</div>
				</div>
			</div>
		</div>
	</div>

	@endsection