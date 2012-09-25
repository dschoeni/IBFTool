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
});

$("#sellPermission").click(function() {
	 $("#sellPermission").addClass('disabled');
});



var currentRound = -1;

var chart = new Highcharts.Chart({
	chart: {
		renderTo: 'permissionPriceChart',
		type: 'line',
		margin: [50, 30, 50, 50],
		width: 460,
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
			enabled: true,
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
		type: "line",
	}]
});

function poll(){
    $.ajax({ url: "/kyoto/update?format=json", success: function(data){
    	
    	if (data.hasStarted == true) {
    		$('#startScreen').modal('hide');
    	}
    	
    	if (data.round != currentRound) {
    		currentRound = data.round;
    		$('#round').text(currentRound);
    		$.ajax({ url: "/kyoto/updatepollution?format=json", success: function(data){
    			$('#otherplayers_minimum').text(data.pollution.minimum);
            	$('#otherplayers_maximum').text(data.pollution.maximum);
            	$('#otherplayers_current').text(data.pollution.current);
            	
            	chart.series[0].remove();
            	chart.addSeries({
                    data: JSON.parse("[" + data.pollution.development + "]"),
                    type: "line"
                });
    			chart.redraw();
    	    }, dataType: "json"});
    		
    		$("#buyPermission").removeClass('disabled');
    		$("#sellPermission").removeClass('disabled');
    		
    	}
    	
        $('#responseData').text(JSON.stringify(data));
        if (data.registered == true) {
        	$('#player_balance').text(data.playerdata.balance);
        	$('#player_permissions').text(data.playerdata.permissions);
        }
    }, dataType: "json", complete: poll, timeout: 100000});
};

