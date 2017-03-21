var MARGIN_LEFT = 10;
var MARGIN_TOP = 30;
var LINE_HEIGHT = 30;

function drawCurveBetweenPoints(startX, endX, y, ctx){
	ctx.beginPath();
	var yTmp = y - 8;
	ctx.moveTo(startX, yTmp);
	ctx.bezierCurveTo(startX, yTmp - 20, endX, yTmp - 20, endX, yTmp);
	ctx.stroke();
}

function drawCircle(centerX, centerY, fillColor, ctx){
	ctx.beginPath();
	var radius = 2;
	ctx.arc(centerX + radius / 2, centerY - 13, radius, 0, 2*Math.PI);
	ctx.fillStyle = fillColor;
    ctx.fill();
	ctx.stroke();
	ctx.fillStyle = 'black';
}

function drawText(text, lineNumber, ctx){
	ctx.beginPath();
	ctx.stroke();
	ctx.fillText(text, MARGIN_LEFT, lineNumber * LINE_HEIGHT + MARGIN_TOP);
}

function calculateWordCoordinates(text, lineNumber, wordIdx, ctx){
	var y = LINE_HEIGHT * (lineNumber + 1);
	var x = MARGIN_LEFT;		

	var words = text.split(" ");

	for (var i=0; i< wordIdx; i++) {
		x += ctx.measureText(words[i]).width;
		x += ctx.measureText(" ").width;
	}

	x += ctx.measureText(words[wordIdx]).width / 2;

	var point = {
		x: x,
		y: y
	};

	return point;
}

function showDependencies(canvasName, text, dependencyParserResults){
	var canvas = document.getElementById(canvasName);
	var ctx = canvas.getContext("2d");
	ctx.font = "12px Courier";
	ctx.clearRect(0, 0, canvas.width, canvas.height);

	var lineNumber = 0;
	var lastSentence = "";

	for (var i=0; i< dependencyParserResults.length; i++) {

		var dpr = dependencyParserResults[i];

		// text
		if (i == 0) {
			drawText(dpr.sentenceNormalized, lineNumber, ctx);	
			lastSentence = dpr.sentenceNormalized;
		} else if (i > 0 && dpr.sentenceNormalized != lastSentence) {
			lineNumber++;
			drawText(dpr.sentenceNormalized, lineNumber, ctx);	
			lastSentence = dpr.sentenceNormalized;
		}

		// detached adjectives
		if (dpr.adjIdx != -1 && dpr.topicIdx == -1 && dpr.negIdx == -1){
			var point = calculateWordCoordinates(dpr.sentenceNormalized, lineNumber, dpr.adjIdx, ctx);
			drawCircle(point.x, point.y, "black", ctx);
		}

		// adjectives with topics
		if (dpr.topicIdx != -1 && dpr.adjIdx != -1){
			var p1 = calculateWordCoordinates(dpr.sentenceNormalized, lineNumber, dpr.topicIdx, ctx);
			var p2 = calculateWordCoordinates(dpr.sentenceNormalized, lineNumber, dpr.adjIdx, ctx);
			drawCurveBetweenPoints(p1.x, p2.x, p1.y, ctx);
		}

		// adjectives with negations
		if (dpr.negIdx != -1 && dpr.adjIdx != -1) {
			var p1 = calculateWordCoordinates(dpr.sentenceNormalized, lineNumber, dpr.negIdx, ctx);
			var p2 = calculateWordCoordinates(dpr.sentenceNormalized, lineNumber, dpr.adjIdx, ctx);
			drawCurveBetweenPoints(p1.x, p2.x, p1.y, ctx);
		}
	}
}
