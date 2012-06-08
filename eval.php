<?php
	require_once('functions.php');
	include('dbinfo.php');
	connectdb();
	$query = "SELECT * FROM prefs";
        $result = mysql_query($query);
        $accept = mysql_fetch_array($result);
        $query = "SELECT status FROM users WHERE username='".$_SESSION['username']."'";
        $result = mysql_query($query);
        $status = mysql_fetch_array($result);
        if($accept['accept'] == 1 and $status['status'] == 1 and is_numeric($_POST['id'])) {
        	$soln = mysql_real_escape_string($_POST['soln']);
        	$filename = mysql_real_escape_string($_POST['filename']);
        	$lang = mysql_real_escape_string($_POST['lang']);
		if($_POST['ctype']=='new')
			$query = "INSERT INTO `solve` ( `problem_id` , `username`, `soln`, `filename`, `lang`) VALUES ('".$_POST['id']."', '".$_SESSION['username']."', '".$soln."', '".$filename."', '".$lang."')";
		else {
			$tmp = "SELECT attempts FROM solve WHERE (problem_id='".$_POST['id']."' AND username='".$_SESSION['username']."')";
			$result = mysql_query($tmp);
			$fields = mysql_fetch_array($result);
			$query = "UPDATE solve SET lang='".$lang."', attempts='".($fields['attempts']+1)."', soln='".$soln."', filename='".$filename."' WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
		}
		mysql_query($query);
		$socket = fsockopen($compilerhost, $compilerport);
		if($socket) {
			fwrite($socket, $_POST['filename']."\n");
			$soln = str_replace("\n", '$_n_$', treat($_POST['soln']));
			fwrite($socket, $soln."\n");
			$query = "SELECT input, output FROM problems WHERE sl='".$_POST['id']."'";
			$result = mysql_query($query);
			$fields = mysql_fetch_array($result);
			$input = str_replace("\n", '$_n_$', treat($fields['input']));
			fwrite($socket, $input."\n");
			fwrite($socket, $lang."\n");
			$status = fgets($socket);
			$contents = "";
			while(!feof($socket))
				$contents = $contents."\n".fgets($socket);
			if($status == 0) {
				$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
				mysql_query($query);
				$_SESSION['cerror'] = trim($contents);
				header("Location: solve.php?cerror=1&id=".$_POST['id']);
			} else if($status == 1) {
				if(trim($contents) == trim(treat($fields['output']))) {
					$query = "UPDATE solve SET status=2 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
					mysql_query($query);
					header("Location: index.php?success=1");
				} else {
					$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
					mysql_query($query);
					header("Location: solve.php?oerror=1&id=".$_POST['id']);
				}
			}
		} else
			header("Location: solve.php?serror=1&id=".$_POST['id']);
	}
?>
