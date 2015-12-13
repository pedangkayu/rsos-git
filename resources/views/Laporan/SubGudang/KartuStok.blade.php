@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){
			//close_sidebar();
		});
	</script>	
@endsection

@section('title')
	Kartu Stok
@endsection

@section('content')
		
	<div class="row">
		<div class="col-sm-9">
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Priode
						@if($req->waktu == 1)
							{{ Format::nama_bulan($req->bulan) }} {{ $req->tahun }}
						@else
							{{ Format::indoDate($req->dari) }} - {{ Format::indoDate($req->sampai) }}
						@endif
					</h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="row">
						<div class="col-sm-6">
							<p>
								<h3>{{ $barang->nm_barang }}</h3>
								<span class="text-muted">{{ $barang->kode }}</span><br />
							</p>
						</div>
						<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-6">
										<address>
											<strong>Sisa Stok per</strong><br />
											<small class="text-muted">{{ Format::indoDate() }}</small>
											<p>{{ number_format(($barang->in - $barang->out),0,',','.') }} {{ $barang->nm_satuan }}</p>
											<strong>Satuan {{ $barang->nm_satuan }}</strong>
										</address>
									</div>
								</div>

						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<div class="text-right">

						<div class="form-group">
							<a href="{{ url('/lapsubgudang') }}" class="btn btn-block btn-primary">Kembali</a>
						</div>
						
						<div class="btn-group" style="width:100%;">
						  	<button type="button" class="btn btn-primary btn-block dropdown-toggle" data-toggle="dropdown">
						    	Print Out <span class="caret"></span>
						  	</button>
						  	<ul class="dropdown-menu" role="menu">
						    	<li>
						    		<a target="_blank" href="{{ url('/lapsubgudang/printks?' . $param) }}">Print</a>
						    	</li>
						    	<li>
						    		<a target="_blank" href="{{url('/lapsubgudang/printpdf?'. $param)}}">PDF</a>
						    	</li>
						    	<li>
						    		<a target="_blank" href="{{url('/lapsubgudang/printexcel?'. $param)}}">Excel</a>
						    	</li>
						  	</ul>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
	

	<div class="grid simple">
		<div class="grid-title no-border">
			<h4>{{ count($items) }} transaksi ditemukan</h4>
		</div>
		<div class="grid-body no-border">
			
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="22%">No Bon</th>
							<th width="15%">Tanggal</th>
							<th width="15%">Tgl. Exp</th>
							<th width="13%">Transaksi</th>
							<th class="text-right" width="10%">Masuk</th>
							<th class="text-right" width="10%">Keluar</th>
							<th class="text-right" width="10%">Sisa</th>
						</tr>

					</thead>

					<tbody>
						<?php $no = 1; ?>
						@forelse($items as $item)
						<?php $jorok = $item['kondisi'] == 2 ? '&nbsp;&nbsp;&nbsp;&nbsp;' : ''; ?>
						<tr title="{{ $item['parent']->keterangan }}">
							<td>{{ $no }}</td>
							@if($item['tipe'] == 1)
								<td>
									{{ $jorok }} <a target="_blank" href="{{ url('/skb/print/' . $item['parent']->id_skb ) }}">{{ $item['parent']->no_skb }}</a>
								</td>
								<td title="{{ Format::jam($item['parent']->created_at) }}">
									{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
								</td>
								<td>-</td>
							@elseif($item['tipe'] == 2)
								<td>
									{{ $jorok }} <a target="_blank" href="{{ url('/gr/print/' . $item['parent']->id_spbm ) }}">{{ $item['parent']->no_spbm }}</a>
								</td>
								<td title="{{ Format::jam($item['parent']->created_at) }}">
									{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
								</td>
								<td>{{ $barang->tipe == 1 ? Format::indoDate2($item['parent']->tgl_exp) : '' }}</td>
							@elseif($item['tipe'] == 3)
								<td>
									{{ $jorok }} <a target="_blank" href="{{ url('/stockadj/print/' . $item['parent']->id_penyesuaian_stok ) }}">{{ $item['parent']->no_penyesuaian_stok }}</a>
								</td>
								<td title="{{ Format::jam($item['parent']->created_at) }}">
									{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
								</td>
								<td>-</td>
							@elseif($item['tipe'] == 4)
								<td>
									{{ $jorok }} <a target="_blank" href="{{ url('/stockadj/print/' . $item['parent']->id_penyesuaian_stok ) }}">{{ $item['parent']->no_retur }}</a>
								</td>
								<td title="{{ Format::jam($item['parent']->created_at) }}">
									{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
								</td>
								<td>-</td>
							@elseif($item['tipe'] == 5)
								<td>
									{{ $jorok }} <a target="_blank" href="{{ url('/returvendor/print/' . $item['parent']->id_retur ) }}">{{ $item['parent']->no_retur }}</a>
								</td>
								<td title="{{ Format::jam($item['parent']->created_at) }}">
									{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
								</td>
								<td>-</td>
							@endif
							<td>{{ $jenis[$item['tipe']] }}</td>
							<td class="text-right">{{ $item['kondisi'] == 1 ? number_format($item['qty'],0,',','.') : '-' }}</td>
							<td class="text-right">{{ $item['kondisi'] == 2 ? number_format($item['qty'],0,',','.') : '-' }}</td>
							<td class="text-right">{{ number_format($item['sisa'],0,',','.') }}</td>
						</tr>
						<?php $no++; ?>
						@empty
						<tr>
							<td colspan="8">Tidak ditemukan</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>

		</div>
	</div>
	
@endsection