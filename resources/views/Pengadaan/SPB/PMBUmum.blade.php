@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/spb.js') }}"></script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-pmb tr:hover td .link{
			display: block;
		}
	</style>
@endsection

@section('title')
	PMB & PMO
@endsection

@section('content')
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="total-data">{{ number_format($items->total(),0,',','.') }}</span> permohonan <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-striped daftar-pmb">
							<thead>
								<tr>
									<th class="text-middle text-center" width="5%">No.</th>
									<th class="text-middle text-center" width="20%">No PMB/PMO</th>
									<th class="text-middle text-center" width="21%">Pemohon</th>
									<th class="text-middle text-center" width="10%">Surat</th>
									<th class="text-middle text-center" width="17%">Tanggal</th>
									<th class="text-middle text-center" width="17%">Tanggal Approval</th>
									<th class="text-middle text-center" width="10%" class="text-center">Status</th>
								</tr>
							</thead>

							<tbody class="allspb">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr class="spb_{{ $item->id_spb }}">
										<td>{{ $no }}</td>
										<td>
											<div>
												{{ $item->no_spb }}
												{!! empty($item->id_acc) ? '<i class="fa fa-times text-muted pull-right" title="Belum terverifikasi"></i>' : '<i title="Terverifikasi" class="fa fa-check-circle text-success pull-right"></i>' !!}
											</div>
											<div class="link text-muted">
												<small>
													[
														<a href="#" onclick="detailspb({{ $item->id_spb }});" data-toggle="modal" data-target="#detail">Lihat</a>
														@if(in_array($item->status, [2,3]))
														| <a href="#" data-toggle="modal" data-target="#detailSKB" onclick="listviewskb({{ $item->id_spb }});">Lihat SKB</a>
														@endif
														@if($item->status < 2 && Auth::user()->permission > 1)
														| <a href="{{ url('/pmbumum/editspb/' . $item->id_spb) }}">Edit</a> | 
														<a href="javascript:;" onclick="delspb({{ $item->id_spb }});" class="text-danger">Hapus</a>
														@endif
													]
												</small>
											</div>
										</td>
										<td>
											<div>{{ $item->nm_depan }} {{ $item->nm_belakang }}</div>
											<div class="text-muted"><small>Dept : {{ $item->nm_departemen }}</small></div>
										</td>
										<td>{{ $item->tipe == 1 ? 'PMO' : 'PMB' }}</td>
										<td>
											<div>{{ Format::indoDate2($item->created_at) }}</div>
											<div class="text-muted"><small>{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small></div>
										</td>
										<td>
											@if(empty($item->tgl_approval) || $item->tgl_approval == '0000-00-00 00:00:00')
												<center>-</center>
											@else
												<div>{{ Format::indoDate2($item->tgl_approval) }}</div>
												<div class="text-muted"><small>{{ Format::hari($item->tgl_approval) }}, {{ Format::jam($item->tgl_approval) }}</small></div>
											@endif
										</td>
										<td class="text-center">{{ $status[$item->status] }}</td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="7"><div class="">Tidak ditemukan</div></td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div>
							<i title="Terverifikasi" class="fa fa-check-circle text-success"></i> Terverifikasi |
							<i class="fa fa-times text-muted" title="Belum terverifikasi"></i> Belum Terverifikasi
						</div>

						<div class="text-right paginspb">
							{!! $items->render() !!}
						</div>

					</div>
				</div>
			</div>

		</div>
		<div class="col-sm-3">
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border text-center">
					<!-- <a class="btn btn-block btn-primary" href="{{ url('/pmbumum/selectitems') }}"><i class="fa fa-plus"></i> Buat PMB & PMO</a> -->
					<!-- Single button -->
					<div class="btn-group" style="width:100%;">
					  <button type="button" class="btn btn-primary btn-block dropdown-toggle" data-toggle="dropdown">
					    <i class="fa fa-plus"></i> Buat PMB & PMO <span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu" role="menu">
					    <li><a href="{{ url('/pmbumum/selectitemspmb') }}">PMB</a></li>
					    <li><a href="{{ url('/pmbumum/selectitemspmo') }}">PMO</a></li>
					    
					  </ul>
					</div>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<div class="form-group">
						<label>No PMB/PMO</label>
						<input type="text" name="kode" class="form-control">
					</div>

					<div class="form-group">
						<div class="checkbox check-danger">
							<input type="checkbox" name="no_approve" value="1" id="no_approve">
							<label for="no_approve">Belum Verifikasi <span class="total_no_approve" style="background:#ff0000;"></span></label>
						</div>
					</div>

					<div class="form-group">
						<label>Status PMB/PMO</label>
						<select name="status" class="form-control">
							<option value="0">Semua</option>
							<option value="1">Baru</option>
							<option value="2">Proses</option>
							<option value="3">Selesai</option>
						</select>
					</div>

					<div class="form-group">
						<label>Gudang Tujuan</label>
						<select name="gtujuan" class="form-control">
							<option value="0">Semua</option>
							<option value="1">G. Obat</option>
							<option value="2">G. Barang</option>
						</select>
					</div>

					<div class="form-group">
						<label>Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500</option>
						</select>
					</div>

					<div class="form-group">
						<butto class="btn btn-block btn-primary carispb"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>
		</div>
	</div>
@endsection

@section('footer')
	<!-- Modal -->
	<div class="modal fade" id="detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
		      	<div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		        	<h4 class="modal-title" id="myModalLabel">NO <span class="viewkode"></span></h4>
		      	</div>
		      	<div class="modal-body">
		        	<div class="detail-pmb">Memuat...</div>
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Keluar</button>
		        	<span class="btn-acc"></span>
		      	</div>
	    	</div>
	  	</div>
	</div>


	<!-- Modal -->
	<div class="modal fade" id="detailSKB" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title" id="myModalLabel">Surat Keluar Barang</h4>
	      </div>
	      <div class="modal-body">
	        <div class="grid simple">
	        	<div class="grid-title no-border"></div>
	        	<div class="grid-body no-border" id="listdetailSKB">
	        		Memuat...
	        	</div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
	      </div>
	    </div>
	  </div>
	</div>
@endsection