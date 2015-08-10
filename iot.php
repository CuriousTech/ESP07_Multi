<?php
// Simple PHP data logger by CuriousTech
if( $_SERVER['REQUEST_METHOD'] == 'POST'){
	$file_name = 'iot/' . $_POST['name'] . '.xml';

	$file = fopen($file_name, 'w+'); // Open XML file for overwrite
	$date = new DateTime();
	fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>'); // Format xml with server datestamp
	fwrite($file, "\n<date>" . $date->format('Y-m-d H:i:s') . "</date>\n");
	foreach ($_POST as $key => $value) { // Save all keys as <key>value</key>
		fwrite($file, "<" . $key . ">" . $value . "</" . $key . ">\n" );
	}
	fclose($file);

	// Append new entry to log as javascript array compatible
	$file_name = 'iot/' . $_POST['name'] . '.txt';
	$file = fopen($file_name, 'a');
	fwrite($file, "{date:'" . $date->format('Y-m-d\TH:i:sP') . "'"); // ISO date, needs converting to JS
	foreach ($_POST as $key => $value) {
		if($key != 'name')
			fwrite($file, "," . $key . ":'" . $value . "'");
	}
	fwrite($file, "},\n");
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
