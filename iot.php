<?php
// Simple PHP data logger by CuriousTech.  Creates 3 different files on POST.  Reads 1 on GET
if( $_SERVER['REQUEST_METHOD'] == 'POST'){

	// 1. Current data in XML format with server date injection
	$file_name = 'iot/' . $_POST['name'] . '.xml';
	$file = fopen($file_name, 'w+'); // Open XML file for overwrite
	$date = new DateTime();
	fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>');
	fwrite($file, "\n<date>" . $date->format('Y-m-d H:i:s') . "</date>\n");
	foreach ($_POST as $key => $value) { // Save all keys as <key>value</key>
		fwrite($file, "<" . $key . ">" . $value . "</" . $key . ">\n" );
	}
	fclose($file);

	// 2. Append new entry to log as javascript array compatible.  If more than 120, remove first 30
	$file_name = 'iot/' . $_POST['name'] . '.txt';
	$arr = file($file_name);
	if($cnt=count($arr) > 120) // remove older lines
	{
		$file = fopen($file_name, 'w');
		for($i=30;$i<$cnt;$i++)
		{
			fwrite($file, $arr[$i]);
		}
		fclose($file);
	}

	$file = fopen($file_name, 'a');
	fwrite($file, "{date:'" . $date->format('Y-m-d\TH:i:sP') . "'"); // ISO date, needs converting to JS
	foreach ($_POST as $key => $value) {
		if($key != 'name')
			fwrite($file, "," . $key . ":'" . $value . "'");
	}
	fwrite($file, "},\n");
	fclose($file);

	// 3. Peaks file.  Floating point compare
	$file_name = 'iot/' . $_POST['name'] . '_peaks.csv';

	if(file_exists($file_name))
	{
		$file = fopen($file_name, "r");
		$peaks = fgetcsv($file, 20, ",");
		fclose($file);
	}
	else
	{
		$peaks = array(
		    0 => 100.0,
		    1 => 0.0,
		    2 => 100.0,
		    3 => 0.0,
		);
	}
	if(bccomp($peaks[0],$_POST['temp']) > 0) $peaks[0] = $_POST['temp'];
	if(bccomp($peaks[1],$_POST['temp']) < 0) $peaks[1] = $_POST['temp'];
	if(bccomp($peaks[2],$_POST['rh']) > 0) $peaks[2] = $_POST['rh'];
	if(bccomp($peaks[3],$_POST['rh']) < 0) $peaks[3] = $_POST['rh'];

	$file = fopen($file_name, "w");
	fputcsv($file, $peaks, ",");
	fclose($file);

}else if( $_SERVER['REQUEST_METHOD'] == 'GET'){
	if($_GET == true && $_GET['name'] == true)
	{
		$file_name = 'iot/' . $_GET['name'] . '.xml';
		if( file_exists($file_name) ) {
			$array = file($file_name);
			foreach($array as $line)
			{
				echo $line;
			}
		}
	}
}
?>
