<?php // create a php as name=iot/file.php that can forward get field data
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	echo "Start\n";
	echo "IP: " . $ip . "\n";
	$date = new DateTime();
	echo "Time: " . $date->format('Y-m-d H:i:s') . "\n";

	if( $_SERVER['REQUEST_METHOD'] == 'GET' && array_key_exists('name', $_GET) ){
		$file_name = 'iot/' . $_GET['name'] . '.php';
		$file = fopen($file_name, 'w+');
		if(array_key_exists('port', $_GET)) $ip = $ip.':'.$_GET['port'];
		$sr = '$s=\'\';if( $_SERVER[\'REQUEST_METHOD\'] == \'GET\'){$arr=new ArrayObject($_GET);$i=0;while($p=current($arr)){if($i==0) $s=\'?\';else $s=$s.\'&\';$s=$s.key($arr).\'=\'.$p;next($arr);$i++;}}';
		fwrite($file, "<?php\n" . $sr . "\nheader(\"Location: http://" . $ip . '/".$s);' . "\nexit;\n?>");
		fclose($file);
	}
?>
