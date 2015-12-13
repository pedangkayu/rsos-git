@extends('Master.Print')

@section('meta')
<style type="text/css">
	h3{
		font-weight: normal;
		margin: 0;
	}
</style>
@endsection

@section('content')

<center>
	<h3><strong>Rekap Belanja Barang dan Obat</strong></h3>
	<span>Periode
	@if($req->waktu == 1)
		{{ Format::nama_bulan($req->bulan) }} {{ $req->tahun }}
	@else
		{{ Format::indoDate2($req->dari) }} - {{ Format::indoDate2($req->sampai) }}
	@endif</span>
</center>
<br />
<table class="table table-bordered" cellspacing = "0">
	<thead>
		<tr>
			<th width="5%" class="text-middle">No.</th>
			<th width="55%" class="text-middle">Barang</th>
			<th width="10%" class="text-right">Jumlah</th>
			<th width="15%" class="text-right">Harga/item</th>
			<th width="15%" class="text-right">Total</th>
		</tr>
	</thead>

	<tbody> 
		@if(count($obats) > 0)
		<?php 
			$no = 1; 
			$reg_total = 0;
            $reg_satuan = 0;
            $reg_nominal = 0;
		?>
			@foreach($obats as $item)
			<tr>
	            <td>{{ $no }}</td>
	            <td>{{ $item->nm_barang }}</td>
	            <td class="text-right">{{ number_format($item->total,0,',','.') }}</td>
	            <td class="text-right">{{ number_format($item->harga_beli,2,',','.') }}</td>
	            <td class="text-right">{{ number_format($item->harga,2,',','.') }}</td>
	        </tr>
	        <?php 
	        	$no++; 
        		$reg_total += $item->total;
                $reg_satuan += $item->harga_beli;
                $reg_nominal += $item->harga;
	        ?>
			@endforeach

			<tr>
                <td colspan="2" class="semi-bold text-center">Total</td>
                <td class="text-right semi-bold">{{ number_format($reg_total,0,'','') }}</td>
                <td class="text-right semi-bold">{{ number_format($reg_satuan,2,',','.') }}</td>
                <td class="text-right semi-bold">{{ number_format($reg_nominal,2,',','.') }}</td>
            </tr>

		@endif


		@if(count($barangs) > 0)
		<?php 
			$no = 1;
			$reg_totalb = 0;
            $reg_satuanb = 0;
            $reg_nominalb = 0;
		?>
			@foreach($barangs as $item)
			<tr>
	            <td>{{ $no }}</td>
	            <td>{{ $item->nm_barang }}</td>
	            <td class="text-right">{{ number_format($item->total,0,',','.') }}</td>
	            <td class="text-right">{{ number_format($item->harga_beli,2,',','.') }}</td>
	            <td class="text-right">{{ number_format($item->harga,2,',','.') }}</td>
	        </tr>
	        <?php 
	        	$no++;
	        	$reg_totalb += $item->total;
                $reg_satuanb += $item->harga_beli;
                $reg_nominalb += $item->harga;
	        ?>
			@endforeach

			<tr>
                <td colspan="2" class="semi-bold text-center">Total</td>
                <td class="text-right semi-bold">{{ number_format($reg_totalb,0,'','') }}</td>
                <td class="text-right semi-bold">{{ number_format($reg_satuanb,0,'','') }}</td>
                <td class="text-right semi-bold">{{ number_format($reg_nominalb,2,',','.') }}</td>
            </tr>

		@endif
	</tbody>
</table>
@endsection