$(function(){

$('.hapusCoa').click(function(){
		var _id 	= $(this).data('id');
		console.log(_id);
		swal({   
			title: "Anda yakin ?",   
			text: "Data akan dihapus secara permanen, dan tidak dapat dikembalikan!",   
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
				url : _base_url + '/coa/destroy',
				data : {id : _id},
				cache : false,
				dataType : 'json',
				success : function(ses){
					if(ses.result == true){
						$('.item_' + _id).addClass('animated hinge', function(){
							setTimeout(function(){
								$('.item_' + _id).remove();
							}, 2000);
							swal("Deleted!", "Data Berhasil dihapus dari Database.", "success");
						});
					}
						
				}
			});

		});
	});

	$('.hapusCoaLedger').click(function(){
		var _id 	= $(this).data('id');
		console.log(_id);
		swal({   
			title: "Anda yakin ?",   
			text: "Data akan dihapus secara permanen, dan tidak dapat dikembalikan!",   
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
				url : _base_url + '/coa/destroyledger',
				data : {id : _id},
				cache : false,
				dataType : 'json',
				success : function(ses){
					if(ses.result == true){
						$('.item_' + _id).addClass('animated hinge', function(){
							setTimeout(function(){
								$('.item_' + _id).remove();
							}, 2000);
							swal("Deleted!", "Data Berhasil dihapus dari Database.", "success");
						});
					}
						
				}
			});

		});
	});

});