<?php

if( $_SERVER['REQUEST_METHOD'] == 'POST'){
	$file_name = 'iot/' . $_POST['name'] . '.xml';

	$file = fopen($file_name, 'w+');
	$date = new DateTime();
	fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>');
	fwrite($file, "\n<date>" . $date->format('Y-m-d H:i:s') . "</date>\n");
	foreach ($_POST as $key => $value) {
		fwrite($file, "<" . $key . ">" . $value . "</" . $key . ">\n" );
	}
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
<html>
<head>
</head>
<body>
</body>
</html>
