@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/prq.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('#hlp').tooltip({
				container: 'body',
				placement : 'left',
				trigger : 'focus',
				title : 'Semua Barang yang bertipe Obat akan di mutasikan ke gudang yang terdaftar pada list gudang di bawah ini.'
			});

			$('[name="deadline"]').datepicker({
					format: "dd-mm-yyyy",
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
	Pengajuan Pembelian Barang
@endsection

@section('content')
	<form method="post" action="{{ url('/prq/additemprq') }}" id="prosesPRQ">
		<input type="hidden" value="{{ csrf_token() }}" name="_token">
		<input type="hidden" value="{{ $tipe }}" name="tipe">

		<div class="row">
			<div class="col-sm-9">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<p>
							<a class="btn btn-primary pull-right" href="{{ url('/prq/select/' . $tipe) }}"><i class="fa fa-plus"></i> Tambah</a>
							<h4>{{ count($items) }} Barang  <strong>terpilih</strong></h4>
						</p>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="30%">Barang</th>
										<th width="15%" class="text-right">Sisa</th>
										<th width="15%" class="text-right">REQ Qty</th>
										<th width="16%">Satuan</th>
										<th width="24%">Keterangan</th>
									</tr>
								</thead>
								<tbody>
									@foreach($items as $item)
										<tr class="item-{{ $item->id_barang }}">
											<td>
												<a href="javascript:;" data-toggle="tooltip" data-placement="bottom" title="{{ $item->nm_barang }}">{{ Format::substr($item->nm_barang,15) }}</a>
												<br /><small class="text-muted">{{ $item->kode }}</small>
												<input type="hidden" value="{{ $item->id_barang }}" name="id_barang[]">
											</td>
											<td class="text-right">{{ number_format(($item->in - $item->out), 0,',','.') }} {{ $item->nm_satuan }}</td>
											<td>
												<input type="number" name="qty[]" class="form-control text-right input-{{ $item->id_barang }}" required>
											</td>
											<td class="satuan-item{{ $item->id_barang }}">
												Memuat...
											</td>
											<td>
												<input type="text" maxlength="50" name="kets[]" class="form-control input-{{ $item->id_barang }}">
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<small class="text-muted">* Mengisi Qty dengan angka 0 sama dengan membatalkan item barang tsb!</small>
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
						
						<div class="form-group">
							<label for="no">No. Pengajuan</label>
							<select name="id_prq" class="form-control" id="no" required>
								<option value="0">Pilih</option>
								@foreach($prqs as $prq)
									<option value="{{ $prq->id_prq }}">{{ $prq->no_prq }}</option>
								@endforeach
							</select>
						</div>

						<p>
							<strong>Keterangan :</strong><br />
							Sebelum anda melanjutkan, silahkan pilih No. Pengajuan yang sudah anda buat sebelumnya<br />
							untuk ditambahkan item barang yang telah anda pilih.
						</p>
					</div>
				</div>

				

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<button type="button" class="btn btn-primary btn-block btn-createprq">Kirim Pengajuan</button>
					</div>
				</div>

			</div>

		</div>
	</form>

@endsection