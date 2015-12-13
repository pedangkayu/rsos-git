@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/modpembelian/vendor/master.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('[name="tanggal"]').datepicker({
				format : 'yyyy-mm-dd'
			});
			$('.btn-tanggal').click(function(){
				$('[name="tanggal"]').val('');
			});
		});
	</script>

	<style type="text/css">
		td > .links{
			display: none;
		}
		table.daftar-vendor tr:hover td .links{
			display: block;
		}
	</style>
@endsection

@section('title')
	Daftar Penyedia
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-striped daftar-vendor">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th width="15%">Kode</th>
									<th width="60%">Instansi</th>
									<th width="20%">Tanggal</th>
								</tr>
							</thead>

							<tbody class="content-vendor">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr title="Klik Kode untuk melihat detail" class="vendor-{{ $item->id_vendor }}">
										<td>{{ $no }}</td>
										<td>
											<a href="#" data-toggle="modal" data-target="#detail" onclick="detail({{ $item->id_vendor }});">{{ $item->kode }}</a>
											@if(Auth::user()->permission > 1)
											<div class="links">
												<small>
													[
														<a href="{{ url('/vendor/edit/' . $item->id_vendor ) }}">Edit</a>
														@if(Auth::user()->permission > 2)
														| <a href="javascript:;" onclick="disabled({{ $item->id_vendor }});" class="text-danger">Disable</a>
														@endif
													]
												</small>
											</div>
											@endif
										</td>
										<td>
											{{ $item->nm_vendor }}
											<div><small class="text-muted"><i class="fa fa-phone"></i> {{ $item->telpon }} </small></div>
										</td>
										<td>
											{{ Format::indoDate($item->created_at) }}
											<div><small class="text-muted">
												{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}
											</small></div>
										</td>
									</tr>

									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="4">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right pagin">
							{!! $items->render() !!}
						</div>
					</div>
				</div>
			</div>	

		</div>
		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a href="{{ url('/vendor/add') }}" class="btn btn-block btn-primary"><i class="fa fa-plus"></i> Tambah Vendor</a>
				</div>
			</div>	

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label for="nm_vendor">Instansi</label>
						<input type="text" name="nm_vendor" class="form-control" id="nm_vendor">
					</div>

					<div class="form-group">
						<label for="kode">Kode</label>
						<input type="text" name="kode" class="form-control" id="kode">
					</div>

					<div class="form-group">
						<label for="tanggal">Tanggal Buat</label>
						<div class="input-group">
					      <input type="text" class="form-control" name="tanggal" id="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tanggal" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>

					<div class="form-group">
						<div class="checkbox check-success">
							<input type="checkbox" name="disabled" id="disabled" value="1">
							<label for="disabled">Disable</label>
						</div>
					</div>

					<div class="form-group">
						<button class="btn btn-primary btn-block btn-cari-vendor"><i class="fa fa-search"></i> Cari</button>
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
	        <h4 class="modal-title" id="myModalLabel">Kode. <span class="modal-code"></span></h4>
	      </div>
	      <div class="modal-body">

	      	<div class="grid simple">
	      		<div class="grid-title no-border"></div>
	      		<div class="grid-body no-border">
	      			<div class="modal-content-vendor">Memuat...</div>
	      		</div>
	      	</div>
	        
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Keluar</button>
	        <span class="modal-btn"></span>
	      </div>
	    </div>
	  </div>
	</div>

@endsection