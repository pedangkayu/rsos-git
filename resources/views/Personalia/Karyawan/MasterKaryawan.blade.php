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
Master Karyawan
@endsection

@section('content')

<div class="row">
	<div class="col-sm-9">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>{{ $items->total() }} Karyawan <span class="semi-bold">ditemukan</span></h4>
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
							<th><a href="javascript:;">Tgl Bergabung</a></th>
							<th><a href="javascript:;">Status</a></th>
							<th></th>
						</tr>
					</thead>
					<tbody class="contents-items">
						<?php $no = $items->currentPage() == 1 ? 1 : ($items->perPage() + $items->currentPage()) -1 ; ?>
						@forelse($items as $item)
						<tr class="item_{{ $item->id_karyawan }} items">
							<td>{{ $no }}</td>
							<td>
								<a href="javascript:;" title="{{ $item->nm_depan }}" data-toggle="tooltip" data-placement="bottom">{{ $item->nm_depan }} {{ $item->nm_belakang }} </a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
										<a href="{{ url('karyawan/review/'.$item->id_karyawan) }}">Lihat</a>
										@if(Auth::user()->permission > 2)
										<!-- hanya admin yang memiliki akses Write dan Execute -->
										| <a href="{{ url('karyawan/update/'.$item->id_karyawan) }}">Edit</a> 
										@endif
										]</small>
									</div>
								</td>
								<td>{{ $item->NIK }}</td>
								<td>{{ $item->nm_jabatan }}</td>
								<td>
								{{ Format::indoDate(date('y-m-d',strtotime($item->tgl_bergabung))) }}
								<small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small>
									
								</td>
								<td>{{ $item->nm_status }}</td>
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

					<div class="text-right pagins">
						{!! $items->render() !!}
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4></h4>
					<div class="tools">
						<a href="javascript:;" class="collapse"></a> 
					</div>
				</div>
				<div class="grid-body no-border">
					<p>
						<a class="btn btn-block btn-primary" href="{{ url('/karyawan/add') }}"><i class="fa fa-plus"></i> Tambah Karyawan</a>
					</p>
					<p>
						<a class="btn" href="{{ url('/karyawan/pending') }}"><span class="badge badge-important">{{ $pending }}</span> Karyawan Pending</a>
					</p>
				</div>

			</div>
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Cari <span class="semi-bold">Karyawan</span></h4>
					<div class="tools">
						<a href="javascript:;" class="collapse"></a> 
					</div>
				</div>
				<div class="grid-body no-border">
					<p>
						<div class="form-group">
							<div class="input-group transparent">
								<span class="input-group-addon ">
									<i class="fa fa-search"></i>
								</span>
								<input type="text" class="form-control" placeholder="Cari Karyawan..." name="src">
							</div>
						</div>

						<div class="form-group">
							<label>NIK</label>
							<input type="text" class="form-control" name="kode">
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