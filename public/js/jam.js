$(function(){

   jam = function(){
      var currentTime = new Date();
      var h = currentTime.getHours();
      var m = currentTime.getMinutes();
      var s = currentTime.getSeconds();
      if (h == 0){
         h = 24;
      }
      if (h < 10){
         h = "0" + h;
      }
      if (m < 10){
         m = "0" + m;
      }
      if (s < 10){
         s = "0" + s;
      }
      var myClock = document.getElementById('jam');
      myClock.textContent = h + ":" + m + ":" + s + "";    
      setTimeout('jam()',1000);
   }

   jam();

});