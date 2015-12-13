$(function(){
	///////////////////// WEB SOCKET /////////////////////
	var socket = io(__SOCKET_HOST);
    socket.on("test-channel:App\\Events\\NotifEvent", function(message){
         // increase the power everytime we load test route
         $('#power').text(parseInt($('#power').text()) + parseInt(message.data.power));
     });
});

