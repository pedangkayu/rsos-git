@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/stockadj.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			agree = function(){
				swal('Peringatan!', 'Pastikan data yang dimasukan sudah benar! Setelah penyesuaian dibuat, Penyesuaian tidak bisa diperbaharui dan dibatalkan.');
			}	

			$('#hlp').tooltip({
				container: 'body',
				placement : 'left',
				trigger : 'focus',
				title : 'Semua Barang yang bertipe Obat akan di mutasikan ke gudang yang terdaftar pada list gudang di bawah ini.'
			});

			$('[name="tanggal"]').datepicker({
					format: "yyyy-mm-dd",
					autoclose: true,
					todayHighlight: true
		   });

			$('[type="number"]').change(function(){
				var n = $(this).val();
				if(n < 0)
					$(this).val(0);
			});

			$.getJSON(_base_url + '/pmbumum/satuans', {

				ids : {!! $ids !!},
				tipe : {{ $tipe }}

			}, function(json){
				for(var i=0; i < json.ids.length; i++){
					if(json.result[json.ids[i]] == true){
						$('.satuan-item' + json.ids[i] ).html(json.content[json.ids[i]]);
					}else{
						$('.satuan-item' + json.ids[i] ).html('<div class="text-center" title="Satuan belum di seting silahkan hubungi Logistik"><i class="fa fa-times text-danger"></i> no set</div>');
						$('.input-' + json.ids[i]).attr('disabled', 'disabled').removeAttr('required').removeAttr('name');
					}
				}

			});
		});
	</script>
@endsection

@section('title')
	Penyesuaian Stok
@endsection

@section('content')
	<form method="post" action="{{ url('/stockadj/create') }}" id="prosesPRQ">
		<input type="hidden" value="{{ csrf_token() }}" name="_token">
		<input type="hidden" value="{{ $tipe }}" name="tipe">

		<div class="row">
			<div class="col-sm-9">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<p>
							<a class="btn btn-primary pull-right" href="{{ url('/stockadj/select/' . $tipe) }}"><i class="fa fa-plus"></i> Tambah</a>
							<h4>{{ count($items) }} Barang  <strong>terpilih</strong></h4>
						</p>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="30%" class="text-middle">Nama</th>
										<th width="15%" class="text-right text-middle">Current Qty</th>
										<th width="15%" class="text-right text-middle">New Qty</th>
										<th width="15%" class="text-middle">Satuan</th>
										<th width="25%" class="text-middle">Keterangan</th>
									</tr>
								</thead>
								<tbody>
									@foreach($items as $item)
										<tr class="item-{{ $item->id_barang }}">
											<td>
												<a href="javascript:;" data-toggle="tooltip" data-placement="bottom" title="{{ $item->nm_barang }}">{{ Format::substr($item->nm_barang,25) }}</a>
												<div class="text-muted"><small>{{ $item->kode }}</small></div>
												<input type="hidden" value="{{ $item->id_barang }}" name="id_barang[]">
												<input type="hidden" value="{{ $item->id_satuan }}" name="satuan_default[]">
												<input type="hidden" value="{{ ($item->in - $item->out) }}" name="current_qty[]">
											</td>
											<td class="text-right">{{ number_format(($item->in - $item->out), 0,',','.') }} {{ $item->nm_satuan }}</td>
											<td>
												<input type="number" name="qty[]" class="form-control text-right input-{{ $item->id_barang }}" required>
											</td>
											<td class="satuan-item{{ $item->id_barang }}">
												Memuat...
											</td>
											<td>
												<input type="text" maxlength="50" name="kets[]" class="form-control input-{{ $item->id_barang }}" required>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<small class="text-muted">* Mengisi Qty degnan angka 0 sama dengan membatalkan item barang tsb!</small>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-3">
				
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>&nbsp;</h4>
						<div class="tools">
			          		<a href="javascript:;" class="collapse"></a> 
			          	</div>
					</div>
					<div class="grid-body no-border">
						<address>
							<strong>Oleh</strong>
							<p>{{ Me::fullname() }}</p>
							<strong>Tanggal</strong>
							<p>{{ Format::indoDate(date('Y-m-d')) }}</p>
						</address>
					</div>
				</div>


				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">

						<div class="form-group">
							<label for="tanggal">Tanggal Buat</label>
						    <input type="text" class="form-control tgl" name="tanggal" id="tanggal" readonly="readonly" value="{{ date('Y-m-d') }}">
						</div>

						<div class="form-group">
							<textarea class="form-control" name="ket" placeholder="Tambah Keterangan..." rows="4"></textarea>
						</div>
					</div>
				</div>

				@if(Auth::user()->permission > 1)
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<div class="form-group">
							<div class="">
								<label for="agree">
									<input type="checkbox" name="agree" value="1" id="agree" required>
									Saya mengerti <a href="javascript:void(0);" onclick="agree();" class="text-success">dengan keterangan !</a>
								</label>
							</div>
						</div>
						<button type="submit" class="btn btn-primary btn-block">Buat Penyesuaian</button>
					</div>
				</div>
				@endif

			</div>

		</div>
	</form>

@endsection