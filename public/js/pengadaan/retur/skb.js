$(function(){
	
	/*Pagination*/
	allskb = function(page){

		var $no_spb = $('[name="no_spb"]').val();
		var $no_skb	= $('[name="no_skb"]').val();
		var $limit 	= $('[name="limit"]').val();
		var $dep 	= $('[name="departemen"]').val();
		var $tanggal = $('[name="tanggal"]').val();

		$('.content-skb').css('opacity', .3);
		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/returgudang/allskb',
			data 	: {
				page 	: page,
				no_skb 	: $no_skb,
				no_spb 	: $no_spb,
				limit 	: $limit,
				dep 	: $dep,
				tanggal	: $tanggal
			},
			cache 	: false,
			dataType : 'json',
			success : function(json){
				$('.content-skb').html(json.content);
				$('.pagin-skb').html(json.pagin);

				$('div.pagin-skb > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link = $(this).attr('href');
					var $split = $link.split('?page=');
					var $page = $split[1];
					allskb($page);
				});

				$('.content-skb').css('opacity', 1);
				onDataCancel();
			}
		});
	}

	$('div.pagin-skb > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link = $(this).attr('href');
		var $split = $link.split('?page=');
		var $page = $split[1];
		allskb($page);
	});

	$('.cariskb').click(function(){
		allskb(1);
	});

});