$(function(){
	
	/*Pagination*/
	getItems = function(page){
		
		var $kode 	= $('[name="kode"]').val();
		var $item 	= $('[name="nm_barang"]').val();
		var $kat 	= $('[name="id_kategori"]').val();
		var $tipe 	= $('[name="jenis"]').val();
		var $limit 	= $('[name="limit"]').val();

		$('.content-barang').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/pmbumum/allitemspmo',
			data 	: {
				page 	: page,
				kode 	: $kode,
				item 	: $item,
				kat 	: $kat,
				tipe 	: $tipe,
				limit 	: $limit
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				$('.content-barang').html(res.data);
				$('.pagins').html(res.pagin);
				$('.content-barang').css('opacity', 1);
				$('body').css('cursor', 'default');

				onDataCancel();
				
				$('div.pagins > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					getItems($page);
				});
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

	/*Advance Searching*/
	$('.Searching').click(function(){
		getItems(1);
	});
	$('[name="id_kategori"]').change(function(){
		getItems(1);
	});
	$('[name="jenis"]').change(function(){
		getItems(1);
	});
	$('[name="limit"]').change(function(){
		getItems(1);
	});
	$('[name="kode"]').keyup(function(e){
		if(e.keyCode == 13 || $(this).val().length == 0){
			getItems(1);
		}
	});
	$('[name="nm_barang"]').keyup(function(e){
		if(e.keyCode == 13 || $(this).val().length == 0){
			getItems(1);
		}
	});
	/*End Advance Searching*/

});