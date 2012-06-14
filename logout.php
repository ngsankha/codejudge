<?php
/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Logout Script
 */
	session_start();
	$_SESSION=array();
	if(isset($_COOKIE[session_name()])) {
		setcookie(session_name(),"",time()-42000,'/');
	}
	session_destroy();
	header("Location: login.php?logout=1");
?>
