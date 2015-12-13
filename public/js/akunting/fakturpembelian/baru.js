$(function(){

	//close_sidebar();

	$('[name="tanggal"]').datepicker();
	$('[name="duodate"]').datepicker();

	vendors = function(id){
		$.getJSON(_base_url + '/ajax/vendors', { select : id }, function(json){
			$('.supplier').html('<select name="supplier" required id="supplier" style="width:100%;"><option value="">Memuat...</option></select>');
			$('[name="supplier"]').html(json.content);
			$('[name="supplier"]').select2();


			$('[name="supplier"]').change(function(){
				var id = $(this).val();
				$('[name="alamat"]').val('').attr('placeholder', 'Memuat...');
				$.getJSON(_base_url + '/fakturpembelian/alamat', {id : id}, function(json){
					$('[name="alamat"]').val(json.alamat).removeAttr('placeholder');;
				});
			});

		});
	}

	$('.simpan-supplier').click(function(){

		var nm_vendor = $('[name="nm_vendor"]').val();
		var nama_pemilik = $('[name="nama_pemilik"]').val();
		var alamat = $('[name="alamat-supplier"]').val();
		var telpon = $('[name="telpon"]').val();
		var fax = $('[name="fax"]').val();
		var email = $('[name="email"]').val();
		var website = $('[name="website"]').val();

		try{

			if(nm_vendor.length < 1)
				throw "Nama Perusahaan tidak boleh kosong!";
			if(nama_pemilik.length < 1)
				throw "Nama Pemilik tidak boleh kosong!";
			if(alamat.length < 1)
				throw "Nama Alamat tidak boleh kosong!";
			if(telpon.length < 1)
				throw "Nama Telpon tidak boleh kosong!";

    		$(this).button('loading');

			var param = {
				nm_vendor : nm_vendor,
				nama_pemilik : nama_pemilik,
				alamat : alamat,
				telpon : telpon,
				fax : fax,
				email : email,
				website : website
			};

			$.post(_base_url + '/fakturpembelian/addsupplier', param, function(json){
				vendors(json.id_vendor);
				$('[name="alamat"]').val(json.alamat);
				$('[data-toggle="input"]').val('');
				$('#vendor').modal('hide');
				$('.simpan-supplier').button('reset');
				swal('Sukes!', json.nm_vendor + ' berhasil tersimpan!');
			}, 'json')

		}catch(e){
			swal('PERINGATAN!', e);
		}
	});
	
	$('.add-new-blank').click(function(){
		var $id = Math.random();
		var $htm = '\
			<tr onclick="id_delete(' + $id + ');" class="item-barang" data-item="' + $id + '">\
				<td><input type="hidden" value="0" name="id_barang[]"></td>\
				<td><input type="text" name="deskripsi[]" class="form-control" required></td>\
				<td>\
					<input type="number" data-form="qty" value="1" name="qty[]" class="form-control text-right" required>\
					<input type="hidden" name="id_satuan[]" value="0" />\
				</td>\
				<td><input type="number" data-form="diskons" value="0" name="diskons[]" class="form-control text-right" required></td>\
				<td><input type="number" data-form="harga" value="0" name="harga[]" class="form-control text-right" required></td>\
				<td><input type="number" data-form="total" value="0" name="total[]" class="form-control text-right" readonly="readonly" required></td>\
			</tr>\
		';
		$('.content-item').append($htm);
		$('.form-control').keyup(function(e){
			matematika();
		});
	});

	$('.btn-hapus').click(function(){
		var $id = $('[name="id_delete"]').val();
		$('[data-item="' + $id + '"]').remove();
		$('[name="id_delete"]').val(0);
		$('.btn-hapus').hide();

		matematika();
	});

	$('#tab-4 a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});


	/* Load data Barang */
	loaditems = function(page){

		var kode = $('[name="modal-kode-item"]').val();
		var barang = $('[name="modal-barang-item"]').val();

		var param = {
			page : page,
			kode : kode,
			barang : barang
		};

		$('.modal-items-list').css('opacity', .3);

		$.getJSON(_base_url + '/fakturpembelian/loaditems', param, function(json){
			
			$('.modal-items-list').html(json.content);
			$('.modal-items-pagin').html(json.pagin);
			$('.modal-items-list').css('opacity', 1);
			$('body').css('cursor', 'default');

			onDataCancel();

			$('div.modal-items-pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				loaditems($page);
			});
		});
	}
	$('[name="modal-kode-item"], [name="modal-barang-item"]').keyup(function(e){
		if(e.keyCode == 13)
			loaditems(1);
	});
	$('.btn-search-item').click(function(){
		loaditems(1);
	});


	/* Load Purchase Order */
	loadpo = function(page){

		var no_po = $('[name="modal-no_po"]').val();
		var status = $('[name="status-po"]').val();

		var param = {
			page : page,
			no_po : no_po,
			status : status
		};

		$('.modal-po-list').css('opacity', .3);

		$.getJSON(_base_url + '/fakturpembelian/loadpo', param, function(json){
			
			$('.modal-po-list').html(json.content);
			$('.modal-po-pagin').html(json.pagin);
			$('.modal-po-list').css('opacity', 1);
			$('body').css('cursor', 'default');

			onDataCancel();

			$('div.modal-po-pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				loadpo($page);
			});
		});
	}
	$('[name="modal-no_po"]').keyup(function(e){
		if(e.keyCode == 13)
			loadpo(1);
	});
	$('[name="status-po"]').change(function(){
		loadpo(1);
	});
	$('.btn-search-po').click(function(){
		loadpo(1);
	});

	/*  Penambaha Item  */
	add_item = function(id){
		$('.barang-' + id).css('opacity', .3);
		$('.btn-item-' + id).remove();
		$('.item-loading-' + id).removeClass('hide');
		$.getJSON(_base_url + '/fakturpembelian/additem', {id : id}, function(json){
			var $htm = '\
				<tr onclick="id_delete(' + json.id_barang + ');" class="item-barang" data-item="' + json.id_barang + '">\
					<td>\
						' + json.kode + '\
						<input type="hidden" value="' + json.id_barang + '" name="id_barang[]">\
					</td>\
					<td><input type="text" value="' + json.nm_barang + '" name="deskripsi[]" readonly="readonly" class="form-control" required></td>\
					<td>\
						<div class="input-group input-group-sm">\
							<input type="number" data-form="qty" value="1" name="qty[]" class="form-control text-right" required>\
						  	<span class="input-group-addon">' + json.nm_satuan + '</span>\
						  	<input type="hidden" name="id_satuan[]" value="' + json.id_satuan + '" />\
						</div>\
					</td>\
					<td><input type="number"  data-form="diskons" name="diskons[]" value="0" class="form-control text-right" required></td>\
					<td><input type="number" data-form="harga" value="' + number_format(json.harga_beli,0,'','') + '" name="harga[]" class="form-control text-right" required></td>\
					<td><input type="number" readonly="readonly" data-form="total" value="' + number_format(json.harga_beli,0,'','') + '" name="total[]" class="form-control text-right" required></td>\
				</tr>\
			';
			$('.content-item').append($htm);
			$('.barang-' + json.id_barang).remove();

			$('.form-control').keyup(function(e){
				matematika();
			});

			matematika();

		});
	}


	/* Penambahan Item berdasarkan PO */
	add_itempo = function(id){
		$('.po-' + id).css('opacity', .3);
		$('.btn-po-' + id).remove();
		$.getJSON(_base_url + '/fakturpembelian/additempo', {id : id}, function(json){

			$('[data-po="true"]').remove();

			$htm = '';
			for(var i =0; i < json.items.length; i++){
				$htm += '\
					<tr data-po="true" class="item-barang">\
						<td>\
							' + json.items[i].kode + '\
							<input type="hidden" value="' + json.items[i].id_barang + '" name="id_barang[]">\
						</td>\
						<td><input type="text" value="' + json.items[i].nm_barang + '"  name="deskripsi[]" readonly="readonly" class="form-control" required></td>\
						<td>\
							<div class="input-group input-group-sm">\
								<input type="number" data-form="qty" value="' + json.items[i].qty + '" name="qty[]" class="form-control text-right" required>\
							  	<span class="input-group-addon">' + json.items[i].nm_satuan + '</span>\
							  	<input type="hidden" name="id_satuan[]" value="' + json.items[i].id_satuan + '" />\
							</div>\
						</td>\
						<td><input type="number"  data-form="diskons" value="' + number_format(json.items[i].diskon,0,'','') + '" name="diskons[]" class="form-control text-right" required></td>\
						<td><input type="number" data-form="harga" value="' + number_format(json.items[i].harga,0,'','') + '" name="harga[]" class="form-control text-right" required></td>\
						<td><input type="number" readonly="readonly" data-form="total" value="' + number_format(json.items[i].total,0,'','') + '" name="total[]" class="form-control text-right" required></td>\
					</tr>\
				';
			}
			
			$('.content-item').append($htm);
			$('.po-' + json.po.id_po).remove();
			$('[name="no_po"]').val(json.po.no_po);
			$('[name="id_po"]').val(json.po.id_po);
			vendors(json.po.id_po);
			$('#produks').modal('hide');
			//console.log(json.po);
			if(json.po.diskon > 0)
				$('[name="diskon"]').val(number_format(json.po.diskon,0,'',''));
			if(json.po.ppn > 0)
				$('[name="ppn"]').val(number_format(json.po.ppn,0,'',''));
			if(json.po.pph > 0)
				$('[name="pph"]').val(number_format(json.po.pph,0,'',''));
			if(json.po.adjustment > 0)
				$('[name="adjustment"]').val(number_format(json.po.adjustment,0,'',''));

			$('.form-control').keyup(function(e){
				matematika();
			});

			matematika();

		});
	}

	$('.dell-po').click(function(){

		if($('[name="id_po"]').val() > 0){
			loadpo(1);
			vendors();
		}

		$('[data-po="true"]').remove();
		$('[name="no_po"]').val('');
		$('[name="id_po"]').val(0);
		$('[name="diskon"]').val('');

		matematika();
	});

	/* MATEMATIKA */
	matematika = function(){
		var subtotal = 0;
		var diskon = $(':input[name="diskon"]').val();
		
		$(':input[data-form="qty"]').each(function(i){
			/* PENJUMLAHAN */
			var harga = $(':input[data-form="harga"]')[i].value;
			var diskons = ($(':input[data-form="harga"]')[i].value * $(':input[data-form="diskons"]')[i].value) / 100;
			var aftdiskon = harga - diskons;
			var kali = aftdiskon * $(this).val();

			$(':input[data-form="total"]')[i].value = kali;
			subtotal += kali;
		});

		var totaldiskon = (subtotal * diskon) / 100;
		var gaftdiskon = subtotal - totaldiskon;
		var ppn = gaftdiskon * $('[name="ppn"]').val() / 100;
		var total = gaftdiskon + ppn + parseInt($(':input[name="adjustment"]').val());



		$('.faktur-subtotal').html(number_format(subtotal,2,',','.'));
		$('.faktur-diskon').html(number_format(totaldiskon,2,',','.'));
		$('.faktur-ppn').html(number_format(ppn,2,',','.'));
		$('.faktur-total').html(number_format(total,2,',','.'));

		$(':input[name="subtotal"]').val(subtotal);
		$(':input[name="grandtotal"]').val(total);
	}

	$('.form-control').keyup(function(e){
		matematika();
	});

	id_delete = function(id){
		$('[name="id_delete"]').val(id);
		$('.btn-hapus').show();
		$('.item-barang').css('background', 'none');
		$('[data-item="' + id + '"]').css('background', '#ddd');

		loaditems(1);
	}
	
	loaditems(1);
	loadpo(1);
});