@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/js/modpembelian/retur/po.js') }}"></script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.list-po tr:hover td .link{
			display: block;
		}
	</style>
@endsection

@section('title')
	Retur Pembelian
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-body no-border">
					<h4>{{ $items->total() }} PO <strong>ditemukan</strong></h4>
					<div class="table-responsive">
						<table class="table table-striped list-po">
							<thead>
								<tr>
									<th width="10%">No.</th>
									<th width="20%">NO PO</th>
									<th width="20%">Tanggal</th>
									<th width="40%">Vendor</th>
									<th width="10%">Status</th>
								</tr>
							</thead>

							<tbody class="content-po">
								<?php $no = 1; ?>
								@forelse($items as $item)
								<tr>
									<td>{{ $no }}</td>
									<td>
										{{ $item->no_po }}
										<div class="text-muted link">
											<small>
												[<a href="{{ url('/returvendor/cretereture/' . $item->id_po) }}">Proses</a>]
											</small>
										</div>
									</td>
									<td>
										{{ Format::indoDate($item->created_at) }}
										<div><small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small></div>
									</td>
									<td>
										{{ $item->nm_vendor }}
										<div><small class="text-muted">dedline {{ Format::hari($item->deadline) }}, {{ Format::indoDate($item->deadline) }}</small></div>
									</td>
									<td>{{ $status[$item->status] }}</td>
								</tr>
								<?php $no++; ?>
								@empty
									<tr>
										<td colspan="5">Tidak ditemukan</td>
									</tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right po-pagin">
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
					<a class="btn btn-block btn-primary" href="{{ url('/returvendor') }}">Kembali</a>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label>No. PO</label>
						<input type="text" name="no_po" class="form-control">
					</div>

					<div class="form-group">
							<label for="penyedia">Penyedia</label>
							<select style="width:100%;" name="id_vendor" id="penyedia" required>
								<option value="">Loading...</option>
							</select>
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
						<label for="deadline">Deadline</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="deadline" id="deadline" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-deadline" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
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