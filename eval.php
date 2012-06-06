<?php
	require_once('functions.php');
	connectdb();
	$query = "SELECT * FROM prefs";
        $result = mysql_query($query);
        $accept = mysql_fetch_array($result);
        $query = "SELECT status FROM users WHERE username='".$_SESSION['username']."'";
        $result = mysql_query($query);
        $status = mysql_fetch_array($result);
        if($accept['accept'] == 1 and $status['status'] == 1) {
		if($_POST['ctype']=='new')
			$query = "INSERT INTO `solve` ( `problem_id` , `username`, `soln`, `filename`, `lang`) VALUES ('".$_POST['id']."', '".$_SESSION['username']."', '".mysql_real_escape_string($_POST['soln'])."', '".mysql_real_escape_string($_POST['filename'])."', '".$_POST['lang']."')";
		else {
			$tmp = "SELECT attempts FROM solve WHERE (problem_id='".$_POST['id']."' AND username='".$_SESSION['username']."')";
			$result = mysql_query($tmp);
			$fields = mysql_fetch_array($result);
			$query = "UPDATE solve SET lang='".$_POST['lang']."', attempts='".($fields['attempts']+1)."', soln='".mysql_real_escape_string($_POST['soln'])."', filename='".mysql_real_escape_string($_POST['filename'])."' WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
		}
		mysql_query($query);
		$filename = "solution/".$_SESSION['username']."/".$_POST['filename'];
		$fp = fopen($filename, 'w');
		fwrite($fp, $_POST['soln']);
		fclose($fp);
		if($_POST['lang']=='c' and $accept['c'] == 1)
			include('lang/c.php');
		else if($_POST['lang']=='cpp' and $accept['cpp'] == 1)
			include('lang/cpp.php');
		else if($_POST['lang']=='java' and $accept['java'] == 1)
			include('lang/java.php');
		else if($_POST['lang']=='python' and $accept['python'] == 1)
			include('lang/python.php');
		else
			header("Location: solve.php?lerror=1&id=".$_POST['id']);
		$res=lang_compile($filename, $_POST['id']);
		if($res == 1 or $res == 2) {
			$fp = fopen("$filename.err", 'r');
			$contents = fread($fp, filesize("$filename.err"));
			fclose($fp);
		}
		unlink("$filename");
		unlink("$filename.in");
		unlink("$filename.out");
		unlink("$filename.err");
		if($res == 1) {
			$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
			mysql_query($query);
			$_SESSION['cerror'] = nl2br(trim($contents));
			header("Location: solve.php?cerror=1&id=".$_POST['id']);
		} else if($res == 2) {
			$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
			mysql_query($query);
			header("Location: solve.php?oerror=1&id=".$_POST['id']);
		} else if($res == 0) {
			$query = "UPDATE solve SET status=2 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
			mysql_query($query);
			header("Location: index.php?success=1");
		}
	}
?>
