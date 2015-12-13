@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/skb.js') }}"></script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-pmb tr:hover td .link{
			display: block;
		}
	</style>
	<script type="text/javascript">
	$(function(){
		$('[name="deadline"]').datepicker({
			format : 'yyyy-mm-dd'
		});
		$('.deldate').click(function(){
			$('[name="deadline"]').val('');
		});
	})
	</script>
@endsection

@section('title')
	{{ $ket['title'] }}
@endsection

@section('content')
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ number_format($items->total(),0,',','.') }} permohonan <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-striped daftar-pmb">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th width="25%">No PMB/PMO</th>
									<th width="30%">Pemohon</th>
									<th width="5%">Surat</th>
									<th width="20%">Tanggal</th>
									<th width="15%" class="text-center">Status</th>
								</tr>
							</thead>

							<tbody class="allspb">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr class="spb_{{ $item->id_spb }}">
										<td>{{ $no }}</td>
										<td>
											<div>{{ $item->no_spb }} <span class="pull-right">{!! $item->id_acc > 0 ? '<i class="fa fa-check" title="Telah disetujui Kepala"></i>' : '<i class="fa fa-warning" title="Belum disetujui Kepala"></i>' !!}</span></div>
											<div class="link text-muted">
												<small>
													[
														<a href="#" onclick="detailspb({{ $item->id_spb }});" data-toggle="modal" data-target="#detail">Lihat</a>
														<!-- | <a href="{{ url('/pmbumum/printspb/' . $item->id_spb) }}" target="_blank">Print</a> -->
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
											<div>{{ Format::indoDate($item->created_at) }}</div>
											<div class="text-muted"><small>{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small></div>
										</td>
										<td class="text-center">{{ $status[$item->status] }}</td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="6"><div class="">Tidak ditemukan</div></td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right paginspb">
							{!! $items->render() !!}
						</div>

					</div>
				</div>
			</div>

		</div>
		<div class="col-sm-3">
			<!-- <div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a class="btn btn-block btn-primary" href="{{ url('/pmbumum/selectitems') }}"><i class="fa fa-plus"></i> Buat PMB & PMO</a>
				</div>
			</div> -->

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<div class="form-group">
						<label>No PMB/PMO</label>
						<input type="text" name="kode" class="form-control">
					</div>

					<div class="form-group">
						<label>Deadline</label>
						<div class="input-group">
					      <input type="text" class="form-control" name="deadline" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default deldate" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					
					<div class="form-group">
						<label>Departemen</label>
						<select name="departemen" class="form-control">
							<option value="0">Semua</option>
							@foreach($departements as $dep)
								<option value="{{ $dep->id_departemen }}">{{ $dep->nm_departemen }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Status PMB/PMO</label>
						<select name="status" class="form-control">
							<option value="0">Semua</option>
							<option value="1" selected="selected">Baru</option>
							<option value="2">Proses</option>
							<option value="3">Selesai</option>
						</select>
					</div>

					@if(count(Me::accessGudang()) > 1)
					<div class="form-group">
						<label>Surat</label>
						<select name="surat" class="form-control">
							<option value="0">Semua</option>
							<option value="2">PMB</option>
							<option value="1">PMO</option>
						</select>
					</div>
					@endif

					<div class="form-group">
						<label>Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500</option>
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-block btn-primary carispb"><i class="fa fa-search"></i> Cari</button>
						<a href="{{ url('/skb') }}" class="btn btn-primary btn-block">Kembali</a>
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
@endsection