<?php
	function lang_compile($filename, $problemid) {
		$query = "SELECT input, output FROM problems WHERE sl=$problemid";
		$result = mysql_query($query);
		$fields = mysql_fetch_array($result);
		$fp = fopen("$filename.in", 'w');
		fwrite($fp, $fields['input']);
		fclose($fp);
		$cmd = "g++ $filename -o $filename.o -lm";
		$desc = array(
			0 => array('file', "$filename.in", 'r'),
			1 => array('file', "$filename.out", 'w'),
			2 => array('file', "$filename.err", 'w')
		);
		$process = proc_open($cmd, $desc, $pipes);
		if(is_resource($process)) 
			proc_close($process);
		$fp = fopen("$filename.err", 'r');
		$contents = "";
		while(($line = fgets($fp)) !== false) {
			if(substr($line, 0, strlen($filename)) == $filename)
				$contents = "$contents\n$line";
		}
		fclose($fp);
		$fp = fopen("$filename.err", 'w');
		fwrite($fp, $contents);
		fclose($fp);
		if(trim($contents) !== "")
			return 1;
		else {
			$cmd = "$filename.o";
			$desc = array(
				0 => array('file', "$filename.in", 'r'),
				1 => array('file', "$filename.out", 'w'),
				2 => array('file', "$filename.err", 'w')
			);
			$process = proc_open($cmd, $desc, $pipes);
			if(is_resource($process)) 
				proc_close($process);
			$fp = fopen("$filename.out", 'r');
			$contents = fread($fp, filesize("$filename.out"));
			fclose($fp);
			if(trim(treat($contents)) == trim(treat($fields['output'])))
				return 0;
			else
				return 2;
		}
	}
?>
