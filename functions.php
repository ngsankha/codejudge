<?php
/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Common functions used throughout Codejudge
 */
session_start();

// checks if any user is logged in
function loggedin() {
  return isset($_SESSION['username']);
}

// connects to the database
function connectdb() {
  include('dbinfo.php');
  mysql_connect($host,$user,$password);
  mysql_select_db($database) or die('Error connecting to database.');
}

// generates a random alpha numeric sequence. Used to generate salt
function randomAlphaNum($length){
  $rangeMin = pow(36, $length-1);
  $rangeMax = pow(36, $length)-1;
  $base10Rand = mt_rand($rangeMin, $rangeMax);
  $newRand = base_convert($base10Rand, 10, 36);
  return $newRand;
}

// gets the name of the event
function getName(){
  connectdb();
  $query="SELECT name FROM prefs";
  $result = mysql_query($query);
  $row = mysql_fetch_array($result);
  return $row['name'];
}

// converts text to an uniform only '\n' newline break
function treat($text) {
	$s1 = str_replace("\n\r", "\n", $text);
	return str_replace("\r", "", $s1);
}
