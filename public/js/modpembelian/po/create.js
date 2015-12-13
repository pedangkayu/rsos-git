$(function(){

	getprq = function(page){

		var $barang 	= $('[name="barang"]').val();
		var $no_prq 	= $('[name="no_prq"]').val();
		var $deadline 	= $('[name="deadline"]').val();
		var $tanggal 	= $('[name="tanggal"]').val();
		var $status 	= $('[name="status"]').val();
		var $titipan 	= $('[name="titipan"]').prop('checked');
		var $vendor 	= $('[name="vendor"]').val();
		var $limit 		= $('[name="limit"]').val();

		try{

			if($titipan == true && $vendor == 0)
				throw 'Silahkan untuk menentukan Penyedia tujuannya!';


			$('.item-prq').css('opacity', .3);
			$.getJSON(_base_url + '/po/prq', {

				barang		: $barang,
				page 		: page,
				no_prq		: $no_prq,
				deadline 	: $deadline,
				tanggal 	: $tanggal,
				status 		: $status,
				titipan		: $titipan,
				vendor 		: $vendor,
				limit 		: $limit

			}, function(json){

				// console.log(json);

				$('.total-find').html(json.total);
				$('.item-prq').html(json.content);
				$('.item-pagin').html(json.pagin);

				$('.item-prq').css('opacity', 1);

				onDataCancel();

				$('div.item-pagin > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					getprq($page);
				});
			});

		}catch(e){
			swal('Peringatan!',e);
		}

	}

	

	$('.btn-cari-prq').click(function(){
		getprq(1);
	});

	$('[type="text"]').keyup(function(e){
		if(e.keyCode == 13)
			getprq(1);
	});

	/* Select Item Prq */
	add = function(id){

		$('.item-prq-' + id).css('opacity', .3);
		
		var $vendor = $('[name="vendor"]').val();

		$.post(_base_url + '/po/addprq', {

			id : id,
			titipan : $vendor

		}, function(json){
			//console.log(json);
			if(json.status == true){
				$('.item-prq-' + json.id).fadeOut('slow', function(){
					$(this).remove();
				});
			}else{
				swal('Peringatan!', json.err);
				$('.item-prq-' + json.id).css('opacity', 1);
			}

			selected();
		}, 'json');

	}

	selected = function(){
		$.getJSON(_base_url + '/po/selected',{},function(json){
			$('.select-total').html(json.total);
			$('.selected-item').html(json.content);

			if(json.total > 0){
				$('.btn-po').removeClass('hide');
			}else{
				$('.btn-po').addClass('hide');
			}
		});
	}

	delselected = function(id){
		$('.prq_item_' + id).css('opacity', .3);
		$.post(_base_url + '/po/delselected', {id : id}, function(json){
			// console.log(json);
			selected();
			getprq(1);
		}, 'json');
	}

	dellall = function(){
		$(this).button('loading');
		$.post(_base_url + '/po/dellall', {}, function(json){
			$('.dellAll').button('reset');
			selected();
			getprq(1);
		}, 'json');
	}

	$('.dellAll').click(function(){
		dellall();
	});
	
	selected();
	getprq(1);
});