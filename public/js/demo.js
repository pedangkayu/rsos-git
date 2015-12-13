$(document).ready(function() {

	//$('#left-panel').addClass('animated bounceInRight');
	$('#project-progress').css('width', '50%');
	$('#msgs-badge').addClass('animated bounceIn');	
	
	$('#my-task-list').popover({
		html:true			
	});

	// $('[data-menu="mainmenu"]').each(function(){
	// 	if($(this).find('ul li.active').length == 1){
	// 		$(this).find('li').addClass('start active open');
	// 		$(this).find('li a span.arrow').addClass('open');
	// 	}
	// });


});