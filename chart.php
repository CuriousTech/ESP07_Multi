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

	// dates
	for(var i = 0; i < data.values.length; i ++) {
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
.style1 {
	color: #FF0000;
}
.style2 {
	color: #0000FF;
}
.style3 {
	color: #00FF00;
}
.style4 {
	color: #00FFFF;
}
</style>
    </head>
    <body>
    	<h1>IOT Chart</h1>
		<span class="style1">Temperature</span> <span class="style2">&nbsp;Humidity</span>
		<span class="style3">&nbsp;</span><span class="style4">Volts</span><br>
    	<br>
        <canvas id="graph" width="800" height="280">   
        </canvas> 
    </body>
</html>
