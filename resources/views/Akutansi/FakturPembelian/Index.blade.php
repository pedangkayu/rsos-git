@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/akunting/fakturpembelian/index.js') }}"></script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-faktur tr:hover td .link{
			display: block;
		}
	</style>
@endsection

@section('title')
	Faktur Pembelian
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="total-faktur">{{ $items->total() }}</span> faktur <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					
					<table class="table table-striped daftar-faktur">
						<thead>
							<tr>
								<th width="10%">No.</th>
								<th width="25%">No. Faktur</th>
								<th width="20%" class="text-right">Total</th>
								<th width="15%">Tanggal</th>
								<th width="15%">Duo Date</th>
								<th width="15%">Status</th>	
							</tr>
						</thead>

						<tbody class="content-faktur">
							<?php $no =1; ?>
							@forelse($items as $item)
							<tr class="faktur-{{ $item->id_faktur }}">
								<td>{{ $no }}</td>
								<td>
									{{ $item->nomor_faktur }}
									<div class="link">
										<small>
											[
												<a href="{{ url('/fakturpembelian/view/' . $item->id_faktur) }}">Lihat</a> |
												<a href="{{ url('/fakturpembelian/edit/' . $item->id_faktur) }}">Edit</a> |
												<a target="_blank" href="{{ url('/fakturpembelian/print/' . $item->id_faktur) }}">Print</a> |
												<a href="javascript:void(0);" onclick="hapus({{ $item->id_faktur }});" class="text-danger">Batal</a>
											]
										</small>
									</div>
								</td>
								<td class="text-right">{{ number_format($item->total,0,',','.') }}</td>
								<td>
									{{ Format::indoDate2($item->tgl_faktur) }}<br />&nbsp;
								</td>
								<td>
									{{ Format::indoDate2($item->duodate) }}<br />&nbsp;
								</td>
								<td>{{ $status[$item->status] }}</td>
							</tr>
							<?php $no++; ?>
							@empty
							<tr>
								<td colspan="6">Tidak ditemukan</td>
							</tr>
							@endif
						</tbody>

					</table>

					<div class="pagin text-right">
						{!! $items->render() !!}
					</div>

				</div>
			</div>

		</div>

		<!-- right -->
		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a href="{{ url('/fakturpembelian/baru') }}" class="btn btn-primary btn-block">Buat Faktur</a>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label for="no_faktur">No. Faktur</label>
						<input type="text" id="no_faktur" name="no_faktur" class="form-control">
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
						<label for="duodate">Duo Date</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="duodate" id="duodate" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-duodate" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="status">Status</label>
						<select class="form-control" name="status" id="status">
							<option value="-">Semua</option>
							<option value="0">Baru</option>
							<option value="1">Nyicil</option>
							<option value="2">Lunas</option>
							<option value="3">Batal</option>
						</select>
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select class="form-control" name="limit" id="limit">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-primary btn-block cari"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>

		</div>
		
	</div>

@endsection