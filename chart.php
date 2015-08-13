<!DOCTYPE html>
<html>
    <head>
        <title>IOT Chart</title>
        <script src="jquery-1.6.1.min.js"></script>
        <script>

var graph;
var xPadding = 30;
var yPadding = 100;
var yRange;
 
var data = { values:[
<?php
	if($_GET == true && $_GET['name'] == true)
	{
		$file_name = 'iot/' . $_GET['name'] . '.txt';
		if( file_exists($file_name) ) {
			$array = file($file_name);
			foreach($array as $line)
			{
				echo $line;
			}
		}
	}
?>
]};

$(document).ready(function() {
	graph = $('#graph');
	var c = graph[0].getContext('2d');

	var tipCanvas = document.getElementById("tip");
    var tipCtx = tipCanvas.getContext("2d");
	var tipDiv = document.getElementById("popup");

    var canvasOffset = graph.offset();
    var offsetX = canvasOffset.left;
    var offsetY = canvasOffset.top;

	c.lineWidth = 2;
	c.strokeStyle = '#333';
	c.font = 'italic 8pt sans-serif';
	c.textAlign = "left";

	c.beginPath(); // borders
	c.moveTo(xPadding, 0);
	c.lineTo(xPadding, graph.height() - yPadding);
	c.lineTo(graph.width() - 40, graph.height() - yPadding);
	c.lineTo(graph.width() - 40, 0);
	c.stroke();

	c.lineWidth = 1;
	// dates
	step = Math.floor(data.values.length / 15)
	if(step == 0) step = 1
	for(var i = 0; i < data.values.length; i += step) {
		if( data.values[i].temp != 'nan')
		{
			c.save()
			c.translate(getXPixel(i), graph.height() - yPadding + 5)
			c.rotate(45)
			date = new Date(isoDateToJsDate(data.values[i].date))
			c.fillText(date.toLocaleString(), 0, 0)
			c.restore()
		}
	}

	yRange = (getMaxY() - getMinY()).toFixed();
	// value range
 	c.textAlign = "right"
	c.textBaseline = "middle";

	for(var i = getMinY(); i < getMaxY(); i += (yRange / 8) ) {
		c.fillText(i.toFixed(1), xPadding - 6, getYPixel(i));
	}

	// volts
	c.fillText('Temp', xPadding-4, 4 );
	c.fillText('Volts', graph.width(), 4 );
	c.fillText('Rh', graph.width() - 26, 4 );
	for(var i = 0; i < 6; i ++ ) {
		pos = graph.height() - 8 - ( ((graph.height() - yPadding) / 6) * i )  - yPadding;
		c.fillText(i, graph.width() - 10, pos );
	}

	// rh
	for(var i = 0; i < 10; i +=2 ) {
		pos = graph.height() - 8 - ( ((graph.height() - yPadding) / 10) * i )  - yPadding;
		c.fillText(i*10, graph.width() - 26, pos );
	}

	// temp lines
	c.strokeStyle = '#f00';
	c.beginPath();
	c.moveTo(getXPixel(0), getYPixel(data.values[0].temp));
 
	for(var i = 1; i < data.values.length; i ++) {
		if( data.values[i].temp != 'nan')
			c.lineTo(getXPixel(i), getYPixel(data.values[i].temp));
	}
	c.stroke();

	// rh lines
	c.strokeStyle = '#00f';
	c.beginPath();
	c.moveTo(getXPixel(0), getRHPixel(data.values[0].rh));

	for(var i = 1; i < data.values.length; i ++) {
		if( data.values[i].temp != 'nan')
			c.lineTo(getXPixel(i), getRHPixel(data.values[i].rh));
	}
	c.stroke();

	// volt lines
	c.strokeStyle = '#0ff';
	c.beginPath();
	c.moveTo(getXPixel(0), getVPixel(data.values[0].volts));

	for(var i = 1; i < data.values.length; i ++) {
		if( data.values[i].temp != 'nan')
			c.lineTo(getXPixel(i), getVPixel(data.values[i].volts));
	}
	c.stroke();

	// data points
	c.fillStyle = '#333'; 
	for(var i = 0; i < data.values.length; i ++) {  
	    c.beginPath();
		if( data.values[i].temp != 'nan')
		    c.arc(getXPixel(i), getYPixel(data.values[i].temp), 3, 0, Math.PI * 2, true);
	    c.fill();
	}

	// data points
	for(var i = 0; i < data.values.length; i ++) {  
	    c.beginPath();
		if( data.values[i].temp != 'nan')
		    c.arc(getXPixel(i), getRHPixel(data.values[i].rh), 3, 0, Math.PI * 2, true);
	    c.fill();
	}

	// volt points
	for(var i = 0; i < data.values.length; i ++) {  
	    c.beginPath();
		if( data.values[i].temp != 'nan')
		    c.arc(getXPixel(i), getVPixel(data.values[i].volts), 3, 0, Math.PI * 2, true);
	    c.fill();
	}
	
	var dots = [];
    for(var i = 0; i < data.values.length; i ++) {
        dots.push({
            x: getXPixel(i),
            y: getYPixel(data.values[i].temp),
            r: 4,
            rXr: 16,
            color: "red",
            tip: data.values[i].temp,
            tip2: data.values[i].rh,
            tip3: data.values[i].date
        });
    }


 // request mousemove events
    graph.mousemove(function(e){handleMouseMove(e);});

    // show tooltip when mouse hovers over dot
    function handleMouseMove(e){
      mouseX=parseInt(e.clientX-offsetX);
      mouseY=parseInt(e.clientY-offsetY);

      // Put your mousemove stuff here
      var hit = false;
      for (var i = 0; i < dots.length; i++) {
          var dot = dots[i];
          var dx = mouseX - dot.x;
          var dy = mouseY - dot.y;
          if (dx * dx + dy * dy < dot.rXr) {
			tipCtx.fillStyle = "#C0C0C0";
			tipCtx.fillRect(0, 0, tipCanvas.width, tipCanvas.height);

			tipCtx.lineWidth = 2;
			tipCtx.fillStyle = "#000000";
			tipCtx.strokeStyle = '#333';
			tipCtx.font = 'italic 8pt sans-serif';
			tipCtx.textAlign = "left";
		
			tipCtx.beginPath(); // borders
			tipCtx.moveTo(0, 0);
			tipCtx.lineTo(0, 60);
			tipCtx.lineTo(90, 60);
			tipCtx.lineTo(90, 0);
			tipCtx.lineTo(0, 0);
			tipCtx.stroke();

			tipCtx.fillText( dot.tip + '°F', 5, 15);
			tipCtx.fillText( dot.tip2 + '%', 5, 29);
			tipCtx.fillText( dot.tip3.slice(0, 10), 5, 43);
			tipCtx.fillText( dot.tip3.slice(11, 25), 5, 57);
			hit = true;
			popup = document.getElementById("popup");
			popup.style.top = dot.y + "px";
			popup.style.left = (dot.x-90) + "px";
          }
      }
      if (!hit) { popup.style.left = "-200px"; }
    }
});

function getMaxY() {
	var max = 0;

	for(var i = 0; i < data.values.length; i ++) {
		if(data.values[i].temp != 'nan' && data.values[i].temp > max)
			max = data.values[i].temp;
	}

//	max += 2 - (max % 10);
	return Math.floor(max) + 1;
}

function getMinY() {
	var min = 200;

	for(var i = 0; i < data.values.length; i ++) {
		if(data.values[i].temp != 'nan' && data.values[i].temp < min)
			min = data.values[i].temp;
	}

//	min -= 10 - min % 10;
	return Math.floor(min);
}
 
function getXPixel(val) {
	return ((graph.width() - 60 - xPadding ) / data.values.length) * val + (xPadding * 1.5);
}

function getYPixel(val) {
	return graph.height() - 6 - ( ((graph.height() - yPadding) / yRange) * ( val-getMinY() ))  - yPadding;
}

function getRHPixel(val) {
	return graph.height() - (((graph.height() - yPadding) / 100) * val) - yPadding;
}

function getVPixel(val) {
	return graph.height() - 8 - (((graph.height() - yPadding) / 6) * val) - yPadding;
}

function isoDateToJsDate(value) 
{
	var a = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)(?:([\+-])(\d{2})\:(\d{2}))?Z?$/.exec(value)
	if (a)
	{
		var utcMilliseconds = Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4], +a[5], +a[6])
		if( a[7] == '-' ) utcMilliseconds += a[8] * (1000 * 60 * 60)
		else utcMilliseconds -= a[8] * (1000 * 60 * 60)
		return new Date(utcMilliseconds)
	}
	return value
}

</script>
<style type="text/css">
#wrapper {
  width: 800px;
  height: 280px;
  position: relative;
}
#graph {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
}
#popup {
  width: 90;
  height: 60;
  position: absolute;
  top: 150px;
  left: 150px;
  z-index: 10;
}
</style>
    </head>
    <body>
	<div id="wrapper">
    <canvas id="graph" width="800" height="280"></canvas>
    <div id="popup" width="90" height="60"><canvas id="tip" width="90" height="60"></canvas></div>
    </div>
    </body>
</html>
