// "GET host/setip.php?name=blah" creates "/iot/blah.php" as a redirector to your device
<?php
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  // try to get real IP
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	if( $_SERVER['REQUEST_METHOD'] == 'GET'){
		$file_name = 'iot/' . $_GET['name'] . '.php';
		$file = fopen($file_name, 'w+');
		fwrite($file, "<?php\nheader(\"Location: http://" . $ip . "/\");\nexit;\n?>");
		fclose($file);
	}
?>
