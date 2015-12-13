@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection

@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/js/akunting/fakturpembelian/view.js') }}"></script>
	<style>
		.datepicker{z-index:1151 !important;}
	</style>
@endsection

@section('title')
FAKTUR #{{ $faktur->nomor_faktur }}
@endsection

@section('content')

<div class="row">
	<!-- left -->
	<div class="col-sm-9">

		<div class="grid simple header-status">
			<div class="grid-title no-border"></div>
			<div class="grid-body no-border">

				<div class="row">
					<div class="col-sm-7">

						<p>
							<div class="status-faktur">
								<span class="label label-{{ $status[$faktur->status]['label'] }}">
									{{ $status[$faktur->status]['err'] }}
								</span>
							</div>
						</p>

						<address>
							<strong>Terima dari.</strong>
							<h4>{{ $faktur->nm_vendor }}</h4>
							<p>KODE #{{ $faktur->kode }}</p>
							<p>
								{{ $faktur->alamat }}<br />
								Telpon :{{ $faktur->telpon }}<br />
								Email :{{ $faktur->email }}
							</p>
						</address>	
						<p><em>"{{ $faktur->keterangan }}"</em></p>
					</div>
					<div class="col-sm-5 text-right">
						<address>
							@if($faktur->id_po > 0)
							<strong>No. PO</strong>
							<p>{{ $faktur->nomor_type }}</p>
						</tr>
						@endif
						<strong>Tanggal</strong>
						<p>{{ Format::indoDate($faktur->tgl_faktur) }}</p>
						<strong>Tanggal Jatuh Tempo</strong>
						<p>{{ Format::indoDate($faktur->duodate) }}</p>
						<strong>Total</strong>
						<h4>{{ number_format($faktur->total,2,',','.') }}</h4>
						<strong>Amount Due</strong>
						<h4>{{ number_format(($faktur->total - $faktur->amount_due),2,',','.') }}</h4>
					</address>
				</div>
			</div>

		</div>
	</div>


	<div class="grid simple">
		<div class="grid-title no-border"></div>
		<div class="grid-body no-border">

			<table class="table">
				<thead>
					<tr>
						<th width="5%">No.</th>
						<th width="30%">Barang</th>
						<th class="text-right" width="15%">Qty</th>
						<th class="text-right" width="10%">Diskon</th>
						<th class="text-right" width="20%">Harga</th>
						<th class="text-right" width="20%">Total</th>
					</tr>
				</thead>

				<tbody>
					<?php $no = 1; ?>
					@foreach($items as $item)
					<tr>
						<td align="center">{{ $no }}</td>
						<td>{{ $item->deskripsi }}</td>
						<td align="right">{{ $item->qty }} {{ $item->nm_satuan }}</td>
						<td align="right">{{ number_format($item->diskon,0,',','.') }}%</td>
						<td align="right">{{ number_format($item->harga,2,',','.') }}</td>
						<td align="right">{{ number_format($item->total,2,',','.') }}</td>
					</tr>
					<?php $no++; ?>
					@endforeach

					<?php 
					/* Matematika */
					$disikon = ($faktur->subtotal * $faktur->diskon) / 100;
					$aftdiskon = $faktur->subtotal - $disikon;
					$ppn = ($aftdiskon * $faktur->ppn) / 100;
					?>

					<tr>
						<td colspan="4" rowspan="7"></td>
						<td align="right" class="bold">Subtotal</td>
						<td align="right">{{ number_format($faktur->subtotal,2,',','.') }}</td>
					</tr>
					<tr>
						<td align="right" class="bold">Diskon {{ number_format($faktur->diskon,1,',','.') }}%</td>
						<td align="right">{{ number_format($disikon,2,',','.') }}</td>
					</tr>
					<tr>
						<td align="right" class="bold">PPN {{ number_format($faktur->ppn,1,',','.') }}%</td>
						<td align="right">{{ number_format($ppn,2,',','.') }}</td>
					</tr>
					<tr>
						<td align="right" class="bold">Adjustment</td>
						<td align="right">{{ number_format($faktur->adjustment,2,',','.') }}</td>
					</tr>
					<tr>
						<td align="right" class="bold">Total</td>
						<td align="right" class="bold">{{ number_format($faktur->total,2,',','.') }}</td>
					</tr>

					<tr>
						<td align="right" class="bold"><h5><strong>Total Bayar</strong></h5></td>
						<td align="right" class="bold"><h5><strong>{{ number_format($total_bayar,2,',','.') }}</strong></h5></td>
					</tr>

					<tr>
						<td align="right" class="bold">Amount Due</td>
						<td align="right" class="bold">{{ number_format(($faktur->total - $faktur->amount_due),2,',','.') }}</td>
					</tr>

				</tbody>

			</table>	

		</div>
	</div>

	<div class="grid simple">
		<div class="grid-title no-border">
			<h4>Relasi <strong>Pembayaran</strong></h4>
		</div>
		<div class="grid-body no-border">

			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="15%">Tanggal</th>
						<th width="30%">Akun</th>
						<th width="35%">Deskripsi</th>
						<th width="20%" class="text-right">Total</th>
					</tr>
				</thead>

				<tbody>
					@forelse($jurnals as $jurnal)
					<tr>
						<td>{{  Format::indoDate2($jurnal->tanggal) }}</td>
						<td>{{ $jurnal->akun }}</td>
						<td>{{ $jurnal->deskripsi }}</td>
						<td class="text-right">{{ number_format($jurnal->total,2,',',',') }}</td>
					</tr>
					@empty
					<tr>
						<td colspan="4">Tidak ditemukan</td>
					</tr>
					@endforelse
				</tbody>
			</table>

		</div>
	</div>

</div>

<!-- right -->
<div class="col-sm-3">

	<div class="grid simple">
		<div class="grid-title no-border"></div>
		<div class="grid-body no-border">

			<button data-toggle="modal" data-target="#payment" class="btn btn-danger btn-block" type="button"><i class="pull-left fa fa-plus"></i> Add Payment</button>
			<a class="btn btn-primary btn-block" href="{{ url('/fakturpembelian/edit/' . $faktur->id_faktur) }}"><i class="pull-left fa fa-pencil"></i> Perbaharui</a>
			<a class="btn btn-primary btn-block" target="_blank" href="{{ url('/fakturpembelian/print/' . $faktur->id_faktur) }}"><i class="pull-left fa fa-print"></i> Print</a>

		</div>
	</div>

	<div class="grid simple">
		<div class="grid-title no-border"></div>
		<div class="grid-body no-border">

			<div class="form-group">
				<div class="btn-group" style="width:100%;">
					<button type="button" class="btn btn-block btn-info dropdown-toggle" data-toggle="dropdown">
						Tandai Sebagai <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="javascript:void(0);" onclick="setstatus(0);">Unpaid</a></li>
						<li><a href="javascript:void(0);" onclick="setstatus(2);">Paid</a></li>
						<li><a href="javascript:void(0);" onclick="setstatus(1);">Partially Paid</a></li>
						<li><a href="javascript:void(0);" onclick="setstatus(3);">Cancel</a></li>
					</ul>
				</div>
			</div>

			<a class="btn btn-white btn-block" href="{{ url('/fakturpembelian') }}"><i class="pull-left fa fa-arrow-circle-left"></i> Kembali</a>

		</div>
	</div>

</div>

</div>

@endsection

@section('footer')
<!-- Modal -->
<div class="modal fade" data-backdrop="static" id="payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form method="post" action="{{ url('/fakturpembelian/savejurnal') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="id_faktur" value="{{ $faktur->id_faktur }}">
			<input type="hidden" name="total_old" value="{{ $faktur->total - $faktur->amount_due }}">

			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Payment</h4>
				</div>
				<div class="modal-body">

					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="coa">Akun BANK *</label>
										<select style="width:100%;" name="id_coa_ledger" id="coa" required>
											<option value="">-Account-</option>
											{!! $select_coa !!}
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="Perkiraan">Akun Perkiraan *</label>
										<select style="width:100%;" name="perkiraan" id="Perkiraan" required>
											<option value="">-Perkiraan-</option>
											{!! $select_coa !!}
										</select>
									</div>		
								</div>
							</div>

							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label for="total">Total *</label>
										<div class="input-group transparent">
											<span class="input-group-addon">Rp.</span>
											<input type="text" name="total" id="total" name="deskripsi" required class="form-control" value="{{ $faktur->total - $faktur->amount_due }}">
										</div>
									</div>		
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="tanggal">Tanggal *</label>
										<div class="input-group transparent">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<input type="text" value="{{ date('m/d/Y') }}" required name="tanggal" id="tanggal" class="form-control" readonly="readonly">
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="id_payment_methode">Method *</label>
										<select style="width:100%;" name="id_payment_methode" id="id_payment_methode" required>
											<option value="">-Method-</option>
											@foreach($methods as $method)
											<option value="{{ $method->id_payment_method }}">&nbsp;{{ $method->payment_method }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="deskripsi">Deskripsi *</label>
								<input type="text" id="deskripsi" name="deskripsi" required class="form-control" value="Faktur #{{ $faktur->nomor_faktur }} Payment">
							</div>

						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-danger">Payment</button>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection