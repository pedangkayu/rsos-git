$(function(){

	$('.hapus').click(function(){
		var _id 	= $(this).data('id');
		var _name 	= $(this).data('nama');
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
				url : _base_url + '/recruitment/destroy',
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
	});

	/*Pagination*/
	getItems = function(page){
		
		

		$('.contents-items').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/recruitment/allitems',
			data 	: {
				page 	: page
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				$('.contents-items').html(res.data);
				$('.pagins').html(res.pagin);
				$('.contents-items').css('opacity', 1);
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

});