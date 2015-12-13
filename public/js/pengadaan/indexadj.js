$(function(){

	/*Pagination*/
	alladj = function(page){
		
		var $kode 		= $('[name="no_bon"]').val();
		var $tanggal 	= $('[name="tanggal"]').val();
		var $tipe 		= $('[name="jenis"]').val();
		var $limit 		= $('[name="limit"]').val();

		$('.item-adj').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/stockadj/alladj',
			data 	: {
				page 	: page,
				kode 	: $kode,
				tanggal : $tanggal,
				tipe 	: $tipe,
				limit 	: $limit
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				$('.item-adj').html(res.content);
				$('.pagin').html(res.pagin);
				$('.item-adj').css('opacity', 1);
				$('body').css('cursor', 'default');

				onDataCancel();
				
				$('div.pagin > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					alladj($page);
				});
			}
		});

	}
	
	$('div.pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		alladj($page);
	});
	/*End Pagination*/

	$('.cari').click(function(){
		alladj(1);
	});

});