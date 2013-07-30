<?php
/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Compiler PHP Script
 */

	require_once('functions.php');
	include('dbinfo.php');
	connectdb();
	$attempts = 0;
	$query = "SELECT * FROM prefs";
        $result = mysql_query($query);
        $accept = mysql_fetch_array($result);
        $query = "SELECT status FROM users WHERE username='".$_SESSION['username']."'";
        $result = mysql_query($query);
        $status = mysql_fetch_array($result);
        // check if the user is banned or allowed to submit and SQL Injection checks
          if($status['status'] == 1 and is_numeric($_POST['id'])) {
        //if($accept['end'] >time() and $status['status'] == 1 and is_numeric($_POST['id'])) {
        	$soln = mysql_real_escape_string($_POST['soln']);
        	$filename = mysql_real_escape_string($_POST['filename']);
        	$lang = mysql_real_escape_string($_POST['lang']);

        	//check if entries are empty
        	if(trim($soln) == "" or trim($lang) == "")
        		header("Location: solve.php?derror=1&id=".$_POST['id']);
        	else {
			if($_POST['ctype']=='new'){
				
				// add to database if it is a new submission
				$query = "INSERT INTO `solve` ( `problem_id` , `username`, `soln`, `lang`, `time`) VALUES ('".$_POST['id']."', '".$_SESSION['username']."', '".$soln."', '".$lang."', '".time()."')";
			}	
			else {
				
				// update database if it is a re-submission
				$query = "UPDATE solve SET time='".time()."', lang='".$lang."', attempts=attempts+1, soln='".$soln."', filename='".$filename."' WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
			}
			mysql_query($query);
			switch($lang) {
			    case 'c': $ext='c'; break;
			    case 'cpp': $ext='cpp'; break;
			    case 'java': $ext='java'; break;
			    case 'python': $ext='py'; break;
			}

			// connect to the java compiler server to compile the file and fetch the results
			$socket = fsockopen($compilerhost, $compilerport);
			if($socket) {
				// for id of row of the submitted answer
                $query="SELECT sl FROM solve WHERE username='".$_SESSION['username']."' and problem_id='".$_POST['id']."'";	
                $result=mysql_query($query);
                $fields=mysql_fetch_array($result);
                fwrite($socket, $fields['sl']."\n");
				$status = fgets($socket);
				$contents = "";
				while(!feof($socket))
					$contents = $contents.fgets($socket);
				    // do here something the databas is updated and the code is compiled  
			} else
				header("Location: solve.php?serror=1&id=".$_POST['id']); // compiler server not running
		}
	}
?>
