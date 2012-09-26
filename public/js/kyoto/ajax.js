var currentRound = -1;

$('#startScreen').modal();
$('#startScreen').modal('show');

$("#startPolling").click(function() {
	 $("#startPolling").addClass('disabled');
	  poll();
});

$('#chartTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

$("#buyPermission").click(function() {
	$("#buyPermission").addClass('disabled');
	$.ajax({ url: "/kyoto/buy?format=json&price=" + $("#buyPrice").val() + "&quantity=" + $("#buyQuantity").val(), success: function(data){
		if(data.result == "success") {

		} else {
		  $("#buyPermission").removeClass('disabled');
		  alert(data.result);
		}
    }, dataType: "json"});
});

$("#sellPermission").click(function() {
	$("#sellPermission").addClass('disabled');
	$.ajax({ url: "/kyoto/sell?format=json&price=" + $("#sellPrice").val() + "&quantity=" + $("#sellQuantity").val(), success: function(data){
		if(data.result == "success") {

		} else {
		  $("#sellPermission").removeClass('disabled');
		  alert(data.result);
		}
    }, dataType: "json"});
});

var chart = new Highcharts.Chart({
	chart: {
		renderTo: 'permissionPriceChart',
		type: 'line',
		margin: [50, 30, 50, 50],
		width: 460
	},
	title: { text: 'Pricedevelopment of Permissions' },
	subtitle: { text: '' },
	tooltip: { enabled: true },
	xAxis: {
		min: 0,
		max: 20,
		allowDecimals: false,
		labels: {
            formatter: function() {
                return this.value;
            }
        }
	},
	yAxis: {
		min: 0,
		max: 500,
		title: {
			text: 'Price'
		},
		stackLabels: {
			enabled: true
		},
		plotLines: [{
			value: 0,
			width: 1,
			color: '#808080'
		}]
	},
	legend: { enabled: false },
	exporting: { enabled: false },
	series: [{
		data: [],
		type: "line"
	}]
});

function poll(){
    $.ajax({ url: "/kyoto/update?format=json", success: function(data){
    	var time = data.session.currentRoundTime;
    	$('#timer').text(time - Math.round(+new Date()/1000) + 60);
    	
    	if (data.session.currentround != currentRound) {
    		currentRound = data.session.currentround;
    		$('#round').text(currentRound);
    		
    		$.ajax({ url: "/kyoto/updatepollution?format=json", success: function(data){
    			$('#otherplayers_minimum').text(data.otherpollution.minimum);
            	$('#otherplayers_maximum').text(data.otherpollution.maximum);
            	$('#otherplayers_current').text(data.otherpollution.current);
            	
            	chart.series[0].remove();
            	chart.addSeries({
                    data: JSON.parse("[" + data.otherpollution.development + "]"),
                    type: "line"
                });
    			chart.redraw();
    			
        		switch(data.playerdata.type) {
	    			case "LowPolluter":
	    				fillPolluterFields(data);
	    			break;
	    			case "NGO":
	    				
	    			break;
		    		case "HighPolluter":
		    			fillPolluterFields(data);
		    		break;
        		}

    	    }, dataType: "json"});

    		$("#buyPermission").removeClass('disabled');
    		$("#sellPermission").removeClass('disabled');
    		
    	}
    	
        $('#responseData').text(JSON.stringify(data));
        if (data.registered == true) {
        	$('#player_balance').text(data.playerdata.balance);
        	$('#player_permissions').text(data.playerdata.permissions);
        }

        if (data.hasStarted == true) {
    		$('#startScreen').modal('hide');
    	}
        
    }, dataType: "json", complete: poll, timeout: 100000});
};

function countDown() {
	var time = new Date().getTime() - startingTime;
	$("#timer").html(Math.floor(time / 1000));
	
	if ((time / 1000) > 60) {
		nextRound();
		return;
	}
	roundTimer = setTimeout(countDown,1000);
}

function fillPolluterFields(data) {
	
	var maximumPollution = getMaximumPollution(data);
	var minimumPollution = getMinimumPollution(data);
	
	if (data.playerdata.technologyChanged > 0) {
		$("#changeTechnology").addClass("disabled");
		$("#changeTechnology").removeClass("btn-success");
	}
	
	$('#pollution_minimum').text(minimumPollution);
    $('#pollution_maximum').text(maximumPollution);
	$('#pollution_current').text(data.pollution.pollution);
}

function getMaximumPollution(data) {
	if (data.pollution.hasTechChanged) {
		return ((data.pollution.techChangeRound * data.pollution.maxIncrease * 2) + (data.session.rounds - data.pollution.techChangeRound) * data.pollution.maxIncrease);
	} else {
		return (data.session.rounds * data.pollution.maxIncrease);
	}
}

function getMinimumPollution(data) {
	return (data.pollution.minIncrease * (data.session.rounds - data.session.currentround));
}

$("#changeTechnology").click(function() {
	$.ajax({ url: "/kyoto/changetechnology?format=json&round=" + currentRound, success: function(data){
		$("#changeTechnology").addClass("disabled");
		$("#changeTechnology").removeClass("btn-success");
	}, dataType: "json"});
});

