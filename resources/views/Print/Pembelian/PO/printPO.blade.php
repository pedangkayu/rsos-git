@extends('Master.Print')

@section('meta')
	<style type="text/css">
		.panel{
			border:solid 1px #000;
			margin-bottom: 10px;
		}
		.panel-body{
			padding: 8px;
		}
		h3{
			font-weight: normal;
			margin: 0;
		}
		hr.dotted{
			border: dashed 1px #000;
		}
		table.detail{
			width: 100%;
		}
		table.detail tr td{
			border-right: dashed 1px #000;
			border-bottom: dashed 1px #000;
			padding: 5px;
		}
		table.detail tr:last-child td{
			border-bottom: none;
		}
		table.detail tr td:last-child{
			border-right: none;
		}

		table.detail2{
			width: 100%;
		}
		table.detail2 tr td{
			border-bottom: dashed 1px #000;
			padding: 5px;
		}
		table.detail2 tr:last-child td{
			border-bottom: none;
		}

		.coret{
			text-decoration:line-through;
			color: red;
		}
	</style>
@endsection

@section('content')
	
	<center><h2>PURCHASE ORDER</h2></center>
	@if($po->status == 5)
	<center><strong style="color:#ff0000;">[KADALUARSA]</strong></center>
	@endif
	<section>
		<table style="width:100%;">
			<tr valign="top">
				<!-- left header -->
				<td width="50%">
					<div>
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<h3>{{ $vendor->nm_vendor }}</h3>
									<address>
										<div>{{ $vendor->alamat }}</div>
										<div>Telpon : {{ $vendor->telpon }}</div>
									</address>

									<p>{{ $po->keterangan }}</p>
								</td>
							</tr>
						</table>
					</div>
				</td>

				<!-- right header -->
				<td width="50%">
					<div>
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td width="50%">
									<strong>PO Date</strong>
								</td>
								<td width="50%">
									{{ Format::indoDate($po->created_at) }}
								</td>
							</tr>

							<tr>
								<td width="50%">
									<strong>PO Number</strong>
								</td>
								<td width="50%">
									{{ $po->no_po }}
								</td>
							</tr>

							<tr>
								<td width="50%">
									<strong>SPH Number</strong>
								</td>
								<td width="50%">
									{{ empty($po->no_sph) ? '-' : $po->no_sph }}
								</td>
							</tr>

							<tr>
								<td width="50%">
									<strong>Deadline</strong>
								</td>
								<td width="50%">
									{{ Format::indoDate($po->deadline) }}
								</td>
							</tr>

						</table>
					</div>
				</td>
			</tr>
		</table>
	</section>
	
	<center>
		{!! $po->titipan > 0 ? '<strong>[ Barang titipan ]</strong>' : '' !!}
	</center>

	<section>
		<table class="table table-bordered" cellspacing="0">
			<thead>
				<tr>
					<th>Item</th>
					<th>Description</th>
					<th>Qty</th>
					<th>Satuan</th>
					<th>Unit/price</th>
					<th>Disc %</th>
					<th>Amount</th>
				</tr>
			</thead>

			<tbody>
				@foreach($items as $item)
				<tr>
					<td>{{ $item->nm_barang }}</td>
					<td>{{ $item->keterangan }}</td>
					<td align="right">{{ $item->req_qty }}</td>
					<td align="right">{{ $item->nm_satuan }}</td>
					<td align="right">{{ number_format($item->harga,2,',','.') }}</td>
					<td align="right">{{ number_format($item->diskon,1,',','.') }}</td>
					<td align="right">{{ number_format(( ($item->harga - (($item->harga * $item->diskon) / 100)) * $item->req_qty ),2,',','.') }}</td>
				</tr>
				@endforeach
				
			</tbody>
			
		</table>

		<table width="100%">
			<tr valign="top">
				<td width="70%">
					<div class="panel">
						<div class="panel-body">
							Term of Condition here...
						</div>
					</div>

					<table width="100%">
						<tr align="center">
							<td>Created By</td>
							<td>Approved By</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr  align="center">
							<td>(..............................)</td>
							<td>(..............................)</td>
						</tr>
					</table>

				</td>
				<td width="30%">
					
					<div class="panel">
						<table width="100%" class="detail2">
							<tr>
								<td colspan="5" align="right" width="50%">Sub Total</td>
								<td align="right" width="500%">{{ number_format($mtk['subtotal'],2,',','.') }}</td>
							</tr>

							<tr>
								<td colspan="5" align="right">Disc {{ $po->diskon }}%</td>
								<td align="right">{{ number_format($mtk['diskon'],2,',','.') }}</td>
							</tr>

							<tr>
								<td colspan="5" align="right">Total</td>
								<td align="right"><b>{{ number_format($mtk['aftdiskon'],2,',','.') }}</b></td>
							</tr>
							
						</table>
					</div>

					<div class="panel">
						<table width="100%" class="detail2">
							
							<tr>
								<td colspan="5" align="right" width="50%">PPN {{ $po->ppn }}%</td>
								<td align="right" width="500%">{{ number_format($mtk['ppn'],2,',','.') }}</td>
							</tr>
							
						</table>
					</div>

					<div class="panel">
						<table width="100%" class="detail2">
							<tr>
								<td colspan="5" align="right" width="50%">Adjustment</td>
								<td align="right" width="500%">{{ $po->adjustment }}</td>
							</tr>

							<tr>
								<td colspan="5" align="right">Grand Total</td>
								<td align="right"><b>{{ number_format($mtk['grandtotal'],2,',','.') }}</b></td>
							</tr>
							
						</table>
					</div>

				</td>
			</tr>
		</table>
	</section>

@endsection