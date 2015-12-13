@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/indexadj.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('[name="tanggal"]').datepicker({
				format : 'yyyy-mm-dd'
			});
			$('.tanggal-btn').click(function(){
				$('[name="tanggal"]').val('');
			});
		});
	</script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-adj tr:hover td .link{
			display: block;
		}
	</style>
@endsection

@section('title')
	Penyesuaian Stok
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $items->total() }} penyesuaian <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="table-responsive">
						<table class="table table-striped daftar-adj">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th width="25%">No Penyesuaian</th>
									<th width="40%">Oleh</th>
									<th width="40%">Tanggal Penyesuaian</th>
								</tr>
							</thead>

							<tbody class="item-adj">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr>
										<td>{{ $no }}</td>
										<td>
											{{ $item->no_penyesuaian_stok }}
											<div class="link">
												<small>[
														<a target="_blank" href="{{ url('/stockadj/print/' . $item->id_penyesuaian_stok ) }}">Print</a>
													]
												</small>
											</div>
										</td>
										<td>
											{{ $item->nm_depan }} {{ $item->nm_belakang }}<br />
											<small class="text-muted">{{ Format::indoDate($item->created_at) }} at {{ Format::jam($item->created_at) }}</small>
										</td>
										<td>{{ Format::hari($item->tanggal) }}, {{ Format::indoDate($item->tanggal) }}</td>
									</tr>

									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="4">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<div class="text-right pagin">
						{!! $items->render() !!}
					</div>

				</div>
			</div>

		</div>

		<!-- right -->
		<div class="col-sm-3">
			
			@if(Auth::user()->permission > 1 && $akses > 0)

				@if($akses > 2)
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border text-center">
							
							<div class="btn-group" style="width:100%;">
							  <button type="button" class="btn btn-primary btn-block dropdown-toggle" data-toggle="dropdown">
							    <i class="fa fa-balance-scale"></i>  Buat Penyesuaian <span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu" role="menu">
							    <li><a href="{{ url('/stockadj/select/2') }}">Barang</a></li>
							    <li><a href="{{ url('/stockadj/select/1') }}">Obat</a></li>
							    
							  </ul>
							</div>

						</div>
					</div>
				@else
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<a class="btn btn-block btn-primary" href="{{ url('/stockadj/select/' . $akses) }}"><i class="fa fa-balance-scale"></i> Buat Penyesuaian <span class="spb-notif"></span></a>
						</div>
					</div>
				@endif

			@endif

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<div class="form-group">
						<label for="no">No Penyesuaian</label>
						<input type="text" id="no" name="no_bon" class="form-control">
					</div>

					<div class="form-group">
						<label>Tanggal</label>
						<div class="input-group">
					      <input type="text" class="form-control" name="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default tanggal-btn" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					@if($akses > 2)
					<div class="form-group">
						<label>Jenis Gudang</label>
						<select id="source" style="width:100%" name="tipe">
							<option value="0">Obat & Barang</option>
							<option value="1">Obat</option>
							<option value="2">Barang</option>
						</select>
					</div>
					@endif

					<div class="form-group">
						<label>Limit / Page</label>
						<select name="limit" class="form-control">
							<option value="5">5</option>
							<option value="10" selected>10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500</option>
						</select>
					</div>

					<div class="form-group">
						<butto class="btn btn-block btn-primary cari"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>

		</div>

	</div>

@endsection