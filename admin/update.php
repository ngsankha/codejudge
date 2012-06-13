<?php
	include('../functions.php');
	connectdb();
	if(isset($_POST['action'])){
		if($_POST['action']=='email') {
			if(trim($_POST['email']) == "")
				header("Location: index.php?derror=1");
			else {
				mysql_query("UPDATE users SET email='".mysql_real_escape_string($_POST['email'])."' WHERE username='".$_SESSION['username']."'");
				header("Location: index.php?changed=1");
			}
		} else if($_POST['action']=='password') {
			if(trim($_POST['oldpass']) == "" or trim($_POST['newpass']) == "")
				header("Location: index.php?derror=1");
			else {
				$query = "SELECT salt,hash FROM users WHERE username='admin'";
				$result = mysql_query($query);
				$fields = mysql_fetch_array($result);
				$currhash = crypt($_POST['oldpass'], $fields['salt']);
				if($currhash == $fields['hash']) {
					$salt = randomAlphaNum(5);
					$newhash = crypt($_POST['newpass'], $salt);
					mysql_query("UPDATE users SET hash='$newhash', salt='$salt' WHERE username='".$_SESSION['username']."'");
					header("Location: index.php?changed=1");
				} else
					header("Location: index.php?passerror=1");
			}
		} else if($_POST['action']=='settings') {
			if(trim($_POST['name']) == "")
				header("Location: index.php?derror=1");
			else {
				if($_POST['accept']=='on') $accept=1; else $accept=0;
				if($_POST['c']=='on') $c=1; else $c=0;
				if($_POST['cpp']=='on') $cpp=1; else $cpp=0;
				if($_POST['java']=='on') $java=1; else $java=0;
				if($_POST['python']=='on') $python=1; else $python=0;
				mysql_query("UPDATE prefs SET name='".mysql_real_escape_string($_POST['name'])."', accept=$accept, c=$c, cpp=$cpp, java=$java, python=$python");
				header("Location: index.php?changed=1");
			}
		} else if($_POST['action']=='addproblem') {
			if(trim($_POST['title']) == "" or trim($_POST['problem']) == "")
				header("Location: problems.php?derror=1");
			else {
				$query="INSERT INTO `problems` ( `name` , `text`, `input`, `output`) VALUES ('".mysql_real_escape_string($_POST['title'])."', '".mysql_real_escape_string($_POST['problem'])."', '".mysql_real_escape_string($_POST['input'])."', '".mysql_real_escape_string($_POST['output'])."')";
				mysql_query($query);
				header("Location: problems.php?added=1");
			}
		} else if($_POST['action']=='editproblem' and is_numeric($_POST['id'])) {
			if(trim($_POST['title']) == "" or trim($_POST['problem']) == "")
				header("Location: problems.php?derror=1&action=edit&id=".$_POST['id']);
			else {
				mysql_query("UPDATE problems SET input='".mysql_real_escape_string($_POST['input'])."', output='".mysql_real_escape_string($_POST['output'])."', name='".mysql_real_escape_string($_POST['title'])."', text='".mysql_real_escape_string($_POST['problem'])."'  WHERE sl='".$_POST['id']."'");
				mysql_query($query);
				header("Location: problems.php?updated=1&action=edit&id=".$_POST['id']);
			}
		}
	}
	else if(isset($_GET['action'])){
		if($_GET['action']=='delete' and is_numeric($_GET['id'])) {
			$query="DELETE FROM problems WHERE sl=".$_GET['id'];
			mysql_query($query);
			header("Location: problems.php?deleted=1");
		} else if($_GET['action']=='ban') {
			$query="UPDATE users SET status=0 WHERE username='".mysql_real_escape_string($_GET['username'])."'";
			mysql_query($query);
			header("Location: users.php?banned=1");
		} else if($_GET['action']=='unban') {
			$query="UPDATE users SET status=1 WHERE username='".mysql_real_escape_string($_GET['username'])."'";
			mysql_query($query);
			header("Location: users.php?unbanned=1");
		}
	}	
?>
