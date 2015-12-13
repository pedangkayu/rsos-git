$(function(){

	allpo = function(page){

		var $no_po 		= $('[name="no_po"]').val();
		var $vendor 	= $('[name="vendor"]').val();
		var $tanggal 	= $('[name="tanggal"]').val();
		var $status 	= $('[name="status"]').val();
		var $limit 		= $('[name="limit"]').val();
		var $deadline 	= $('[name="deadline"]').val();
		var $titipan 	= $('[name="titipan"]').prop('checked');
		

		$('.content-po').css('opacity', .3);

		$.getJSON(_base_url + '/po/allpo', {

			page	: page,
			no_po 	: $no_po,
			vendor 	: $vendor,
			tanggal : $tanggal,
			status 	: $status,
			limit 	: $limit,
			titipan : $titipan,
			deadline : $deadline

		}, function(json){
			
			$('.content-po').html(json.content);
			$('.pagin').html(json.pagin);

			$('.content-po').css('opacity', 1);
			onDataCancel();

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allpo($page);
			});

		});

	}

	$('div.pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		allpo($page);
	});

	$('.cari').click(function(){
		allpo(1);
	});

	delpo = function(id){

		swal({   
			title: "Anda yakin ?",   
			text: " Anda yakin ingin menghapusnya ?",
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes, delete it!",   
			closeOnConfirm: true 
		}, function(){
			
			$('.itempo-' + id).css('opacity', .3);
			$.post(_base_url + '/po/delpo', {id : id}, function(json){
				$('.itempo-' + json.id).remove();
			}, 'json');

		});

	}

});