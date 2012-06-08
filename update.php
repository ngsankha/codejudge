<?php
	include('functions.php');
	connectdb();
	if($_POST['action']=='email') {
		mysql_query("UPDATE users SET email='".mysql_real_escape_string($_POST['email'])."' WHERE username='".$_SESSION['username']."'");
		header("Location: account.php?changed=1");
	} else if($_POST['action']=='password') {
		$query = "SELECT salt,hash FROM users WHERE username='".$_SESSION['username']."'";
		$result = mysql_query($query);
		$fields = mysql_fetch_array($result);
		$currhash = crypt($_POST['oldpass'], $fields['salt']);
		if($currhash == $fields['hash']) {
			$salt = randomAlphaNum(5);
			$newhash = crypt($_POST['newpass'], $salt);
			mysql_query("UPDATE users SET hash='$newhash', salt='$salt' WHERE username='".$_SESSION['username']."'");
			header("Location: account.php?changed=1");
		} else
			header("Location: account.php?passerror=1");
	}
?>
