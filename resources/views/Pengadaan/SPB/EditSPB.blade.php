@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/spb.js') }}"></script>
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

			$.getJSON(_base_url + '/pmbumum/satuans', <?php echo $param; ?>, function(json){
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
	Edit PMB & PMO : {{ $spb->no_spb }}
@endsection

@section('content')
	<form method="post" action="{{ url('/pmbumum/editspb') }}">
		<input type="hidden" value="{{ csrf_token() }}" name="_token">
		<input type="hidden" value="{{ $spb->id_spb }}" name="id_spb">
		<input type="hidden" value="{{ $spb->no_spb }}" name="no_spb">

		<div class="row">
			<div class="col-sm-9">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<p>
							<a class="btn btn-primary pull-right" href="{{ url('/pmbumum') }}">Kembali</a>
							<h4>{{ count($items) }} Barang  <strong>terpilih</strong></h4>
						</p>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="20%">Kode</th>
										<th width="20%">Nama</th>
										<!-- <th width="10%">Tipe</th> -->
										<th width="10%" class="text-right">Sisa</th>
										<th width="15%" class="text-right">Qty</th>
										<th width="15%" class="text-right">Satuan</th>
										<th width="25%">Keterangan</th>
										<!-- <th width="5%">MTSI</th> -->
									</tr>
								</thead>
								<tbody>
									@foreach($items as $item)
										<tr class="item-{{ $item->id_barang }}">
											<td>
												{{ $item->kode }}
												<input type="hidden" value="{{ $item->id_barang }}" name="id_barang[]">
												<input type="hidden" value="{{ $item->satuan_default }}" name="id_satuan[]" class="input-{{ $item->id_barang }}">
											</td>
											<td>
												<a href="javascript:;" data-toggle="tooltip" data-placement="bottom" title="{{ $item->nm_barang }}">{{ Format::substr($item->nm_barang,20) }}</a>
											</td>
											<!-- <td>{{ $item->tipe == 1 ? 'Obat' : 'Barang' }}</td> -->
											<td class="text-right">{{ number_format(($item->in - $item->out), 0,',','.') }} {{ $item->nm_satuan }}</td>
											<td>
												<input type="number" name="qty[]" value="{{ $item->qty_lg }}" class="form-control text-right" required>
											</td>
											<td class="satuan-item{{ $item->id_barang }}">
												Memuat...
											</td>
											<td>
												<input type="text" maxlength="50" name="kets[]" value="{{ $item->keterangan }}" class="form-control">
											</td>
											<!-- <td>
												<div class="checkbox check-success checkbox-circle">
													<input id="checkbox{{ $item->id_barang }}" name="mutasi[]" type="checkbox" value="{{ $item->id_barang }}" {{ $item->tipe == 2 ? 'disabled' : '' }} {{ $item->tipe == 2 ? '' : 'checked' }}>
													<label for="checkbox{{ $item->id_barang }}"></label>
												</div>
											</td> -->
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
							<p>{{ $spb->nm_depan }} {{ $spb->nm_belakang }}</p>
							<strong>Tanggal</strong>
							<p>{{ Format::indoDate($spb->created_at) }}</p>
							<strong>Departemen</strong>
							<p>{{ $spb->nm_departemen }}</p>
						</address>
					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">

						<div class="form-group">
							<label>Deadline</label>
			                <input type="text" class="form-control" name="deadline" value="{{ date('d-m-Y', strtotime($spb->deadline)) }}" readonly="readonly">
						</div>

						@if(count($obat) > 0)
						<div class="form-group">
							<label>Mutasi ke Gudang <a href="javascript:;" id="hlp"><i class="glyphicon glyphicon-question-sign"></i></a></label>
							<select name="id_gudang" class="form-control" required>
								<option value="">Pilih Gudang</option>
								@foreach($gudangs as $gudang)
									<option value="{{ $gudang->id_gudang }}" {{ $gudang->id_gudang == $id_gudang ? 'selected' : '' }}>{{ $gudang->nm_gudang }}</option>
								@endforeach
							</select>
						</div>
						@endif

						<div class="form-group">
							<textarea class="form-control" name="ket" placeholder="Tambah Keterangan..." rows="4">{{ $spb->keterangan }}</textarea>
						</div>
					</div>
				</div>

				@if(Auth::user()->permission > 1)
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<button class="btn btn-primary btn-block" type="submit">Simpan Perubahan</button>
					</div>
				</div>
				@endif

			</div>

		</div>
	</form>

@endsection