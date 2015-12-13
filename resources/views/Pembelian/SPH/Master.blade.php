@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/modpembelian/sph/master.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('.tgl').datepicker({
				format : 'yyyy-mm-dd'
			});
			$('.btn-tanggal').click(function(){
				$('.tgl').val('');
			});
		});
	</script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-sph tr:hover td .link{
			display: block;
		}
	</style>
@endsection

@section('title')
	Pengajuan Harga
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $items->total() }} ditemukan</h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="table-responsive">
						<table class="table table-striped daftar-sph">
							<thead>
								<tr>
									<th width="10%">No.</th>
									<th width="20%">No. SPH</th>
									<th width="30%">Oleh</th>
									<th width="30%">Tanggal</th>
									<th width="10%">Status</th>
								</tr>
							</thead>

							<tbody class="content-sph">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr class="sph-{{ $item->id_sph_grup }}">
										<td>{{ $no }}</td>
										<td>
											{{ $item->no_sph }}
											<div class="link">
												<small>
													[
														<a href="{{ url('/sph/review/' . $item->id_sph_grup) }}">Lihat</a>
														@if(Auth::user()->permission > 1 && $item->status == 1)
														| <a href="javascript:;" onclick="hapus({{ $item->id_sph_grup }});" class="text-danger">Hapus</a>
														@endif
													]
												</small>
											</div>
										</td>
										<td>{{ $item->nm_depan }} {{ $item->nm_belakang }}</td>
										<td>
											{{ Format::indoDate($item->created_at) }}
											<div>
												<small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small>
											</div>
										</td>
										<td>
											{{ $status[$item->status] }}
										</td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="5">Tidak ditemukan</td>
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
			@if(Auth::user()->permission > 1)
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a href="{{ url('/sph/select') }}" class="btn btn-block btn-primary">Buat SPH</a>
				</div>
			</div>
			@endif

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label>No. SPH</label>
						<input type="text" name="no_sph" class="form-control">
					</div>

					<div class="form-group">
						<label for="tanggal">Tanggal Buat</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="tanggal" id="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tanggal" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="limit">Status</label>
						<select name="status" class="form-control">
							<option value="0">Semua</option>
							<option value="1">Proses</option>
							<option value="2">selesai</option>
						</select>
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>

					<div class="form-group">
						<button class="cari btn btn-block btn-primary"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>

		</div>
	</div>

@endsection