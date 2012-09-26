
$("#startPolling").click(function() {
	 $("#startPolling").addClass('disabled');
	  poll();
});

function poll(){
	setInterval(function () {
		$.ajax({ url: "/administration/kyoto_server/nextround?format=json", success: function(data){
			
			$("#currentround").html(data.round);
			
	    }, dataType: "json", timeout: 100000});		
	}, 120);
};

