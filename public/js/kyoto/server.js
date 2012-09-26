var startingTime;
var isPaused = false;;

$("#startGame").click(function() {
	$("#startGame").addClass('disabled');
	
	startingTime = new Date().getTime();
	countDown();
});

$("#pauseGame").click(function() {
	$("#startGame").removeClass('disabled');
	startingTime = new Date().getTime();
	clearInterval(roundTimer);
});

var roundTimer;

function countDown() {
	var time = new Date().getTime() - startingTime;
	$("#timer").html(Math.floor(time / 1000));
	
	if ((time / 1000) > 60) {
		nextRound();
		return;
	}
	roundTimer = setTimeout(countDown,1000);
}

function nextRound() {
	$.ajax({
		url : "/administration/kyoto_server/nextround?format=json",
		success : function(data) {
			$("#currentround").html(data.currentround);
			startingTime = new Date().getTime();
			countDown();
		},
		dataType : "json",
		timeout : 100000
	});
}

