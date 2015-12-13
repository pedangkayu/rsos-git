<html>
<tr>
	<td>Kode {{ $barang->tipe == 1 ? 'Obat' : 'Barang' }}</td>
	<td>:</td>
	<td><b>{{ $barang->kode }}</b></td>
</tr>
<tr>
	<td>Nama {{ $barang->tipe == 1 ? 'Obat' : 'Barang' }}</td>
	<td>:</td>
	<td><b>{{ $barang->nm_barang }}</b></td>
</tr>
<tr>
	<td>Gudang</td>
	<td>:</td>
	<td><b>{{ $gudang->nm_gudang }}</b></td>
</tr>
<tr>
	<td>Satuan</td>
	<td>:</td>
	<td><strong>{{ $barang->nm_satuan }}</strong></td>
</tr>

<tr></tr>

<tr>
	<th>No</th>
	<th>No Bon</th>
	<th>Tanggal</th>
	<th>Transaksi</th>
	<th>Oleh</th>
	<th>Masuk</th>
	<th>Keluar</th>
	<th>Sisa</th>
</tr>

<?php $no = 1; ?>
@forelse($items as $item)
<?php $jorok = $item['kondisi'] == 2 ? '&nbsp;&nbsp;&nbsp;&nbsp;' : ''; ?>
<tr>
	<td>{{ $no }}</td>
	@if($item['tipe'] == 1)
	<td>
		{{ $jorok }} {{ $item['parent']->no_skb }}
	</td>
	<td>
		{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
	</td>
	@elseif($item['tipe'] == 2)
	<td>
		{{ $jorok }} {{ $item['parent']->no_spbm }}
	</td>
	<td >
		{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
	</td>
	@elseif($item['tipe'] == 3)
	<td>
		{{ $jorok }} {{ $item['parent']->no_penyesuaian_stok }}
	</td>
	<td >
		{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
	</td>
	@elseif($item['tipe'] == 4)
	<td>
		{{ $jorok }} {{ $item['parent']->no_retur }}
	</td>
	<td >
		{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
	</td>
	@elseif($item['tipe'] == 5)
	<td>
		{{ $jorok }} {{ $item['parent']->no_retur }}
	</td>
	<td >
		{{ $jorok }} {{ Format::indoDate2($item['parent']->created_at) }}
	</td>
	@endif
	<td>{{ $jenis[$item['tipe']] }}</td>
	<td>{{ $item['oleh'] }}</td>
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

</html>	