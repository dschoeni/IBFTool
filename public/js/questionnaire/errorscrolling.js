$(document).ready(function (){
	if ($('.errors').length > 0) {
		$('html, body').animate({
	        scrollTop: $(".errors").offset().top-100
	    }, 2000);
		
		$('.errors').blink({
		    maxBlinks: 5, 
		    blinkPeriod: 300,   // in milliseconds
		    onBlink: function(){}, 
		    onMaxBlinks: function(){}
		});
	}
  });