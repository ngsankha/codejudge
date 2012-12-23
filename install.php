<?php
/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Installation PHP Script
 */
	require_once('functions.php');
	if(isset($_POST['host'])) {
		// create file 'dbinfo.php'
		$fp = fopen('dbinfo.php','w');
		$l1 = '$host="'.$_POST['host'].'";';
		$l2 = '$user="'.$_POST['username'].'";';
		$l3 = '$password="'.$_POST['password'].'";';
		$l4 = '$database="'.$_POST['name'].'";';
		$l5 = '$compilerhost="'.$_POST['chost'].'";';
		$l6 = '$compilerport='.$_POST['cport'].';';
		fwrite($fp, "<?php\n$l1\n$l2\n$l3\n$l4\n$l5\n$l6\n?>");
		fclose($fp);
		include('dbinfo.php');
		// connect to the MySQL server
		mysql_connect($host,$user,$password);
		// create the database
		mysql_query("CREATE DATABASE $database");
		mysql_select_db($database) or die('Error connecting to database.');
		// create the preferences table
		mysql_query("CREATE TABLE IF NOT EXISTS `prefs` (
  `name` varchar(30) NOT NULL,
  `accept` int(11) NOT NULL,
  `c` int(11) NOT NULL,
  `cpp` int(11) NOT NULL,
  `java` int(11) NOT NULL,
  `python` int(11) NOT NULL,
  `formula` text NOT NULL
)");
		// fill it with default preferences
		mysql_query("INSERT INTO `prefs` (`name`, `accept`, `c`, `cpp`, `java`, `python`, `forumla`) VALUES
('Codejudge', 1, 1, 1, 1, 1,'\$score = \$points / \$attempts')");
		// create the problems table
		mysql_query("CREATE TABLE IF NOT EXISTS `problems` (
  `sl` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `input` text NOT NULL,
  `output` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT '3000',
  `points` int(11) NOT NULL,
  PRIMARY KEY (`sl`)
)");
		// create the solve table
		mysql_query("CREATE TABLE IF NOT EXISTS `solve` (
  `sl` int(11) NOT NULL AUTO_INCREMENT,
  `problem_id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `attempts` int(11) NOT NULL DEFAULT '1',
  `soln` text NOT NULL,
  `filename` varchar(25) NOT NULL,
  `lang` varchar(20) NOT NULL,
  `score` float NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`sl`)
)");
		// create the users table
		mysql_query("CREATE TABLE IF NOT EXISTS `users` (
  `sl` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `salt` varchar(6) NOT NULL,
  `hash` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `score` float NOT NULL,
  PRIMARY KEY (`sl`)
)");
		// create the user 'admin' with password 'admin'
		$salt=randomAlphaNum(5);
		$pass="admin";
		$hash=crypt($pass,$salt);
		$sql="INSERT INTO `users` ( `username` , `salt` , `hash` , `email` ) VALUES ('$pass', '$salt', '$hash', '".$_POST['email']."')";
		mysql_query($sql);
		header("Location: install.php?installed=1");
	}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Codejudge Setup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      
      .footer {
        text-align: center;
        font-size: 11px;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="http://twitter.github.com/bootstrap/assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Codejudge Setup</a>
        </div>
      </div>
    </div>

    <div class="container">
    <?php
      if(isset($_GET['installed'])) {?>
        <div class="alert alert-success">Codejudge is successfully installed!</div>
        
        You can login to the admin panel <a href="admin/">here</a> with the password <strong>admin</strong>. You can change it once you login to the admin panel.
    <?php  }else if(!file_exists("dbinfo.php")){ ?>
    Welcome to the Codejudge setup. This will help you set up Codejudge on your server. Make sure that you have MySQL running before you proceed.
    <h1><small>Details</small></h1>
    <form action="install.php" method="post">
    Database Host: <input type="text" name="host" value="localhost"/><br/>
    Username: <input type="text" name="username"/><br/>
    Password: <input type="password" name="password"/><br/>
    Database Name: <input type="text" name="name" value="codejudge"/><br/>
    Email: <input type="email" name="email"/><br/>
    Compiler Server Host: <input type="text" name="chost" value="localhost"/><br/>
    Compiler Server Port: <input type="text" name="cport" value="3029"/><br/>
    <input type="submit" class="btn btn-primary" value="Install"/>
    </form>
    <?php } else {?>
      <div class="alert alert-error">Codejudge is already installed. Please remove the files and re-install it.</div>
    <?php } ?>
    </div> <!-- /container -->

<?php
	include('footer.php');
?>
