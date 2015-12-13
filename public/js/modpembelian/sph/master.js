$(function(){

	allsph = function(page){

		var $no_sph = $('[name="no_sph"').val();
		var $tgl 	= $('[name="tanggal"').val();
		var $limit 	= $('[name="limit"').val();
		var $status = $('[name="status"').val();

		$('.content-sph').css('opacity', .3);

		$.getJSON(_base_url + '/sph/allsph', {

			page 	: page,
			no_sph 	: $no_sph,
			tanggal : $tgl,
			limit 	: $limit,
			status 	: $status

		}, function(json){

			$('.content-sph').css('opacity', 1);

			$('.content-sph').html(json.content);
			$('.pagin').html(json.pagin);

			onDataCancel();

			$('div.pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				allsph($page);
			});
		});

	}

	$('div.pagin > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		allsph($page);
	});

	$('.cari').click(function(){
		allsph(1);
	});


	hapus = function(id){

		swal({   
			title: "Anda yakin ?",   
			text: " Anda yakin ingin menghapus pengajuan ini ?",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes, delete it!",   
			closeOnConfirm: true 
		}, function(){
			
			$('.sph-' + id).css('opacity', .3);
			$.post(_base_url + '/sph/hapussphgrup', {id : id}, function(json){
				$('.sph-' + json.id).remove();
			}, 'json');

		});

		
	}

});