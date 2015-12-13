$(function(){

	$('[name="nm_barang"]').focus();

	/**
	* Confirmasi delete Items 
	* http://t4t5.github.io/sweetalert/
	*/

	hapus = function( _name , _id){
		swal({   
			title: "Anda yakin ?",   
			text: _name + " akan dihapus secara permanen, dan tidak dapat dikembalikan!",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes, delete it!",   
			closeOnConfirm: false 
		}, function(){
			/* Delete with Ajax */
			//$('.item_' + _id).css('opacity', '.3');
			$('.sweet-alert h2').html('Menghapus...');
			$.ajax({
				type : 'POST',
				url : _base_url + '/logistik/destroy',
				data : {id : _id},
				cache : false,
				dataType : 'json',
				success : function(ses){
					if(ses.result == true){
						$('.item_' + _id).addClass('animated hinge', function(){
							setTimeout(function(){
								$('.item_' + _id).remove();
							}, 2000);
							swal("Deleted!", _name + " Berhasil dihapus dari Database.", "success");
						});
					}
						
				}
			});

		});
	}

	/**
	* Menambahkan kolom detail
	*/
	$('.add-detail').click(function(){
		var tmp = '\
			<div class="row">\
				<button type="button" class="close" style="position:absolute; right:50px; z-index:1;" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\
            	<div class="col-sm-6">\
            		<div class="form-group">\
                        <label for="" class="form-label">Label *</label>\
                        <span class="help">e.g. "Merek"</span>\
                        <div class="controls">\
                          <input type="text" class="form-control" name="labels[]" required>\
                        </div>\
                    </div>\
            	</div>\
            	<div class="col-sm-6">\
            		<div class="form-group">\
                        <label for="" class="form-label">Value *</label>\
                        <span class="help">e.g. "Betadine"</span>\
                        <div class="controls">\
                          <input type="text" class="form-control" name="values[]" required>\
                        </div>\
                    </div>\
            	</div>\
            </div>\
		';

		$('.detail-items').append(tmp);
	});
	

	/**
	* Reset Semua form isian
	*/
	$('.reset').click(function(){
		$('input').val('');
		$('select').val('');
		$('.detail-items').html('');
		$('[type="number"]').val(0);
		$('[name="nm_barang"]').focus();
		onDataCancel();
	});

	/**
	* Nilai minimal Form stok ke dalam 0
	*/
	$('[type="number"]').change(function(){
		var _val = $(this).val();
		if(_val < 0) $(this).val(0);
	});


	/**
	* Review Barang
	*/
	review = function(id){
		var body = $('.review-detail');
		var kode = $('.review-kode');
		var load = '<i class="fa fa-circle-o-notch fa-spin"></i> Memuat...';
		body.html(load);
		kode.html('');
		$('.link').html('');
		$.ajax({
			type : 'POST',
			url : _base_url + '/logistik/review',
			data : {id : id},
			cache : false,
			dataType : 'json',
			success : function(res){
				if(res.result == true){
					kode.html(res.kode);
					body.html(res.content);
					$('.link').html(res.link);
				}
			}
		});
	}
	
	/**
	* Mengambil data barang yang sudah limit
	*/
	$.getJSON(_base_url + '/logistik/limitstok', function(json){
		if(json.total > 0)
			$('.badges').html(' <span class="badge">' + json.total + '</span>');
	});

	
	/*Pagination*/
	getItems = function(page){
		
		var $src 	= $('[name="src"').val();
		var $kode 	= $('[name="kode"]').val();
		var $kat 	= $('[name="kat"]').val();
		var $tipe 	= $('[name="tipe"]').val();
		var $sort 	= $('[name="sort"]').val();
		var $orderby = $('[name="orderby"]').val();
		var $limit 	= $('[name="limit"]').val();
		var $limit_stok = $('[name="limit_stok"]').prop('checked');
		
		$('.contents-items').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/logistik/allitems',
			data 	: {
				page 	: page,
				src 	: $src,
				kode 	: $kode,
				kat 	: $kat,
				tipe 	: $tipe,
				sort 	: $sort,
				orderby : $orderby,
				limit 	: $limit,
				stok 	: $limit_stok
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				$('.contents-items').html(res.data);
				$('.pagins').html(res.pagin);
				$('.total-items').html(res.total);
				$('.contents-items').css('opacity', 1);
				$('body').css('cursor', 'default');

				$('div.pagins > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					getItems($page);
				});

				onDataCancel();
				$('[data-toggle="tooltip"]').tooltip();

			}
		});

	}
	
	$('div.pagins > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		getItems($page);
	});
	/*End Pagination*/


	$('.cari-barang').click(function(){
		getItems(1);
	});

	$('.tabah-konversi').click(function(){
		var $htm = $('.master-konversi').html();
		$('.add-konversi').append('<div class="form-group">' + $htm + '</div>');
	});

	log_harga = function(id, page, tipe){

		$('.content-harga').css('opacity', .3);
		$.getJSON(_base_url + '/logistik/logharga', {
			
			id 		: id,
			page 	: page,
			tipe 	: tipe

		}, function(json){
			//console.log(json);
			$('.content-harga').html(json.content);
			$('.pagin-harga').html(json.pagin);

			$('.content-harga').css('opacity', 1);

			$('div.pagin-harga > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				log_harga(id, $page, tipe);
			});

		});
	}

	logexp = function(page, id){
		
		$('.list-exp').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/logistik/listexpired',
			data 	: {
				page 	: page,
				id_barang 	: id
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				$('.list-exp').html(res.content);
				$('.pagin-exp').html(res.pagin);
				$('.list-exp').css('opacity', 1);
				$('body').css('cursor', 'default');

				$('div.pagin-exp > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					logexp($page, res.id_barang);
				});

				onDataCancel();
			}
		});
	}

});