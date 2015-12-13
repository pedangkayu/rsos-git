@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/modpembelian/retur/itempo.js') }}"></script>
@endsection

@section('title')
	Daftar Items / Purchase Order (PO)
@endsection

@section('content')
	
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4><span class="total">0</span> <span class="semi-bold">barang ditemukan</span></h4>
					<div class="tools">
		          		<a href="javascript:allitempo(1);" class="reload" data-toggle="tooltip" data-placement="bottom" title="Refresh"></a> 
		          	</div>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<td width="20%">
										<input type="text" name="kode" placeholder="Kode" class="form-control">
									</td>
									<td width="55%">
										<input type="text" name="nm_barang" placeholder="Nama Obat / Barang" class="form-control">
									</td>
									<td width="30%" colspan="2">
										<button class="btn btn-block btn-primary Searching" title="Advance Searching"><i class="fa fa-search"></i> Cari</button>
									</td>
								</tr>
								<tr class="advance-src">
									<td colspan="4">
										<div class="row">
											
											<div class="col-xs-12">
												<select name="limit" style="width:100%;">
													<option value="10">Limit 10</option>
													<option value="50">Limit 50</option>
													<option value="100">Limit 100</option>
													<option value="500">Limit 500</option>
												</select>
											</div>
										</div>
									</td>
								</tr>
							</thead>
							<tbody class="content-barang">
								<tr>
									<td colspan="3">
										Memuat...
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="pagins text-right">
						
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
					<p>Setiap Item mewakili item yang ada pada PO tersebut.</p>
					<p>
						Setiap Item yang terpilih akan di arahkan pada halaman Purchase Order bersama list item yang bersangkutan dengan PO tersebut.
					</p>
				</div>
			</div>

		</div>
	</div>

@endsection