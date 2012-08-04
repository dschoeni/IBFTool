function mouseover_next() { document.getElementById("next").style.backgroundColor='#ababab'; }
function mouseout_next() { document.getElementById("next").style.backgroundColor='#ffffff'; } 

function mouseover_back() { document.getElementById("back").style.backgroundColor='#ababab'; }
function mouseout_back() { document.getElementById("back").style.backgroundColor='#ffffff'; } 

function drawCharts() {
	var i = 0;
	for (valueArray in userData) {
		$('#charts')
				.append(
						"<div class='left_5050'><div id='chart"
								+ i
								+ "'></div></div><div class='right_5050'><div id='chart_possible_"
								+ i + "'></div></div>");

		chart = new Highcharts.Chart({
			chart : {
				renderTo : 'chart' + i,
				type : 'column',
				margin : [ 50, 20, 30, 50 ],
			},
			title : {
				text : 'Verteilung der realisierten Erträge (' + Math.round(valueArray * 100) + '% Anteil an der Risikoanlage)'
			},
			subtitle : {
				text : ''
			},
			tooltip : {
				enabled : false
			},
			plotOptions: {
				column: {
					pointWidth : 2
				}
			},
			xAxis : {
				min : -15000,
				max : 15000,

				allowDecimals : false,
				labels : {
					formatter : function() {
						return this.value;
					}
				}
			},
			yAxis : {
				min : 0,
				max : 0.12,
				
				tickInterval : 0.01,
				title : {
					text : 'Wahrscheinlichkeit'
				},
				stackLabels : {
					enabled : true,
				},
				plotLines : [ {
					value : 0,
					width : 1,
					color : '#808080'
				} ]
			},
			legend : {
				enabled : false
			},
			exporting : {
				enabled : false
			},
			series : [ {
				data : [],
				type : "column"
			} ]
		});

		chart2 = new Highcharts.Chart({
			chart : {
				renderTo : 'chart_possible_' + i,
				type : 'spline',
				margin : [ 50, 20, 30, 50 ],
			},
			title : {
				text : 'Verteilung der möglichen Erträge (' + Math.round(valueArray * 100)
						+ '% Anteil an der Risikoanlage)'
			},
			subtitle : {
				text : ''
			},
			plotOptions: {
				column: {
					pointWidth : 2
				}
			},
			tooltip : {
				enabled : false
			},
			xAxis : {
				min : -15000,
				max : 15000,

				allowDecimals : false,
				labels : {
					formatter : function() {
						return this.value;
					}
				}
			},
			yAxis : {
				min : 0,
				max : 0.12,
				
				tickInterval : 0.01,
				title : {
					text : 'Wahrscheinlichkeit'
				},
				stackLabels : {
					enabled : true,
				},
				plotLines : [ {
					value : 0,
					width : 1,
					color : '#808080'
				} ]
			},
			legend : {
				enabled : false
			},
			exporting : {
				enabled : false
			},
			series : [ {
				data : [],
			} ]
		});
		
		
		for (var x = -15000; x <= 15000; x=x+200) {
			var normalRandom = normalpdf(1000*valueArray, 4800*valueArray, x);
			chart2.series[0].addPoint({
				marker : {
					enabled: false
				},
				y : normalRandom*150,
				x : x,
			});
		}

		for ( var u = 0; u < userData[valueArray].length; u++) {
			if (chart.get(userData[valueArray][u]) == null) {
				chart.series[0].addPoint({
					id : userData[valueArray][u],
					color : '#259bff',
					y : 1 / userData[valueArray].length,
					x : userData[valueArray][u],
				});
			} else {
				chart.get(userData[valueArray][u]).update(chart.get(userData[valueArray][u]).y+ (1 / userData[valueArray].length), false);
			}
		}

		i++;
	}
	
	$("#dialog").modal("hide");
	
}

$('#drawNewScenario').click(function() {
	// if (!isNaN($('input:radio[name=risk]:checked').val())) {
	chartSingle.series[0].remove();
	drawScenario();

	chartSingle.redraw();
	chart.redraw();

	// } else {
	// $("#dialog").dialog("open");
	// }
});

function rnd_snd() {
	return (Math.random() * 2 - 1) + (Math.random() * 2 - 1)
			+ (Math.random() * 2 - 1);
}

function rnd(mean, stdev) {
	return (rnd_snd() * stdev + mean);
}

function compute(Z) {
	M = 1.1;
	SD = 0.48;
	with (Math) {
		if (SD < 0) {
			alert("The standard deviation must be nonnegative.");
		} else if (SD == 0) {
			if (Z < M) {
				Prob = 0;
			} else {
				Prob = 1;
			}
		} else {
			Prob = normalcdf((Z - M) / SD);
			Prob = round(100000 * Prob) / 100000;
		}
	}
	return Prob;
}

function normalcdf(X) {
	var T = 1 / (1 + .2316419 * Math.abs(X));
	var D = .3989423 * Math.exp(-X * X / 2);
	var Prob = D * T * (.3193815 + T * (-.3565638 + T * (1.781478 + T * (-1.821256 + T * 1.330274))));
	
	if (X > 0) {
		Prob = 1 - Prob;
	}
	
	return Prob;
}

function normalpdf(mean, stddev, x) {
	var r = (1/stddev)*normal((x-mean)/stddev);
	return r;
}

function normal(x) {
	var a = 1/(Math.sqrt(2*Math.PI));
	var e = Math.exp((-0.5)*x*x);
	return a*e;
}
