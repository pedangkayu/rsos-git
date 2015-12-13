$(function(){

	getItems = function(page){
		
		var $src 	= $('[name="src"').val();
		var $kode 	= $('[name="kode"]').val();
		var $kat 	= $('[name="kat"]').val();
		var $tipe 	= $('[name="tipe"]').val();
		var $gudang = $('[name="gudang"]').val();
		var $limit 	= $('[name="limit"]').val();
		var $limit_stok = $('[name="limit_stok"]').prop('checked');
		
		$('.contents-items').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/subgudang/items',
			data 	: {
				page 	: page,
				src 	: $src,
				kode 	: $kode,
				kat 	: $kat,
				gudang	: $gudang,
				tipe 	: $tipe,
				limit 	: $limit,
				stok 	: $limit_stok
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				$('.contents-items').html(res.data);
				$('.pagins').html(res.pagin);
				$('.total-item').html(res.total);
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

	$('.cari-barang').click(function(){
		getItems(1);
	});

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
					$('.stok-barang').hide();
					$('.link').hide();
				}
			}
		});
	}

	$.getJSON(_base_url + '/subgudang/limitstok', {}, function(json){
		if(json.total != 0){
			$('.habis').html(json.total);
		}
	});

});