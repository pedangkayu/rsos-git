$(function(){

	/*Pagination*/
	allitempo = function(page){
		
		var $kode 	= $('[name="kode"]').val();
		var $nm_barang 	= $('[name="nm_barang"]').val();
		var $limit 	= $('[name="limit"]').val();

		$('.content-barang').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/returvendor/allitemspo',
			data 	: {
				page 		: page,
				kode 		: $kode,
				nm_barang 	: $nm_barang,
				limit 		: $limit
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				console.log(res);
				$('.content-barang').html(res.content);
				$('.pagins').html(res.pagin);
				$('.tota').html(res.total);
				$('.content-barang').css('opacity', 1);
				$('body').css('cursor', 'default');

				onDataCancel();
				
				$('div.pagins > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					allitempo($page);
				});
			}
		});

	}
	
	$('.Searching').click(function(){
		allitempo(1);
	});
	
	allitempo();
	/*End Pagination*/

});