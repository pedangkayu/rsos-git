$(function(){
	$('.btn-rating').click(function(){
		var id = $(this).data('id');

		$('.ratings').html('Memuat...');
		$('.ratings-pagin').html('');
		$('.ratings').removeClass('hide');
		$('.ratings-pagin').removeClass('hide');
		$('.detail').addClass('hide');

		getraings(1, id);
	});

	getraings = function(page, id){
		$('.ratings').css('opacity', .3);
		$.getJSON(_base_url + '/vendor/viewrats', {

			page : page,
			id : id

		}, function(json){
			$('.ratings').html(json.content);
			$('.ratings-pagin').html(json.pagin);

			$('[data-rel="ratings"]').rating();

			$('.ratings').css('opacity', 1);

			$('div.ratings-pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				getraings($page, json.id);
			});

		});
	}

	closeRating = function(){
		$('.ratings').html('');
		$('.ratings-pagin').html('');
		$('.ratings').addClass('hide');
		$('.ratings-pagin').addClass('hide');
		$('.detail').removeClass('hide');
	}

});