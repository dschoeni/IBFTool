function mouseover_forward() { document.getElementById("drawNewScenario").style.backgroundColor='#ababab'; }
function mouseout_forward() { document.getElementById("drawNewScenario").style.backgroundColor='#ffffff'; } 

function mouseover_20() { document.getElementById("draw20NewScenario").style.backgroundColor='#ababab'; }
function mouseout_20() { document.getElementById("draw20NewScenario").style.backgroundColor='#ffffff'; } 

function mouseover_next() { document.getElementById("next").style.backgroundColor='#ababab'; }
function mouseout_next() { document.getElementById("next").style.backgroundColor='#ffffff'; } 

var chart;
var userData = {};
$(document).ready(function(){
	$("#plotslider").slider({
				  min: 0,
				  max: 100,
				  value: 50,
				  step: 1,
				  slide: function(event, ui) {
					  $("#plotamount").val(ui.value);
					  
					  chart.series[0].remove();
						chart.addSeries({
					        data: []        
					    });
				  },
	});
	
	$("#plotamount").blur(function() {
		
		if($("#plotamount").val() > 100) {
			$("#plotamount").val(100);
		} else if ($("#plotamount").val() < 0) {
			$("#plotamount").val(0);
		} else if (!is_numeric($("#plotamount").val())) {
			$("#plotamount").val(0);
		}
		
		$("#plotslider").slider( "option", "value",  $("#plotamount").val());
		
		chart.series[0].remove();
		chart.addSeries({
	        data: []        
	    });
	});
	
	
	 $("#dialog").dialog({
	    	bgiframe: true,
	    	autoOpen: false,
	    	height: 300,
	    	modal: true,
	    	buttons: {
	    		Okay: function() {
	    		  $(this).dialog("close");
	    		}
	    	}
	    });
	
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'chart',
			type: 'column',
			margin: [50, 30, 50, 50],
		},
		title: {
			text: 'Erträge unter allen bisher gezogenen Szenarien'
		},
		subtitle: {
			text: ''
		},
		tooltip: {
			enabled: false
		},
		xAxis: {
			min: -15000,
			max: 15000,

			allowDecimals: false,
			labels: {
                formatter: function() {
                    return this.value;
                }
            }
		},
		yAxis: {
			tickInterval: 2,
			title: {
				text: 'Häufigkeit'
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
		plotOptions: {
			column: {
				pointWidth : 2
			}
		},
		legend: {
			enabled: false
		},
		exporting: {
			enabled: false
		},
		series: [{
			data: [],
			type: "column",
		}]
	});

	chartSingle = new Highcharts.Chart({
		chart: {
			renderTo: 'chart_single',
			type: 'column',
			margin: [50, 30, 50, 50],
		},
		tooltip: {
			enabled: false
		},
		plotOptions: {
			column: {
				pointWidth : 2
			}
		},
		title: {
			text: 'Erträge des/der zuletzt gezogenen Szenarien'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			min: -12000,
			max: 14000,
			tickPixelInterval: 100,
		},
		yAxis: {
			tickInterval: 1,
			title: {
				text: 'Häufigkeit'
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
		legend: {
			enabled: false
		},
		exporting: {
			enabled: false
		},
		series: [{
			data: [],
			type: "column",
		}]
	});
	});

$('#drawNewScenario').click(function() {
		//if (!isNaN($('input:radio[name=risk]:checked').val())) {
			chartSingle.series[0].remove();
			drawScenario();
			
			chartSingle.redraw();
			chart.redraw();
			
		//} else {
		//	$("#dialog").dialog("open");
		//}
});	

$('#draw20NewScenario').click(function() {
	//if (!isNaN($('input:radio[name=risk]:checked').val())) {
		chartSingle.series[0].remove();
		
		for (var i = 0; i <= 20; i++) {
			drawScenario();
		}
		
		chartSingle.redraw();
		chart.redraw();
		
	//} else {
	//	$("#dialog").dialog("open");
	//}
});	

function sendData()
{
	$("#hiddenData").val(JSON.stringify(userData));
}

function drawScenario() {
	//var risk = $('input:radio[name=risk]:checked').val();
	var risk = $("#plotslider").slider( "option", "value" )/100;
	var money = 10000;

	var normalRandom = rnd(1.1, 0.48);
	var outputPercentage;
	
	if (normalRandom > 1) {
		outputPercentage = normalRandom - 1;
	} else if (normalRandom < 1) {
		outputPercentage = -(1 - normalRandom);
	} else {
		outputPercentage = 0;
	}
	
	var realOutput = ((money*risk)*outputPercentage)+((0.02)*(money*(1-risk)));
	var output = Math.round(realOutput/200)*200;
	
	
	// update total chart
	if (chart.get(output) == null) {
		chart.series[0].addPoint({
			id: output,
			color: '#259bff',
			y: 1,
			x: output
			});
	} else {
		chart.get(output).update(chart.get(output).y+1, false);
	}
	
	// update single chart
	if (chartSingle.series[0] == null) {
		chartSingle.addSeries({
            data: [],
        });
	}
	
	if (chartSingle.get(output) == null) {
		chartSingle.series[0].addPoint({
			id: output,
			color: '#259bff',
			y: 1,
			x: output
			});
	} else {
		chartSingle.get(output).update(chartSingle.get(output).y+1, false);
	}
	
	if (userData[risk] == null) {
		userData[risk] = new Array();
		userData[risk][0] = output; 
	} else {
		userData[risk][userData[risk].length] = output;
	}
	
}

function rnd_snd() {
	return (Math.random()*2-1)+(Math.random()*2-1)+(Math.random()*2-1);
}

function rnd(mean, stdev) {
	return (rnd_snd()*stdev+mean);
}
