<?php
session_start();

function loggedin() {
  return isset($_SESSION['username']);
}

function connectdb() {
  include('dbinfo.php');
  mysql_connect($host,$user,$password);
  mysql_select_db($database) or die('Error connecting to database.');
}

function randomAlphaNum($length){
  $rangeMin = pow(36, $length-1);
  $rangeMax = pow(36, $length)-1;
  $base10Rand = mt_rand($rangeMin, $rangeMax);
  $newRand = base_convert($base10Rand, 10, 36);
  return $newRand;
}

function getName(){
  connectdb();
  $query="SELECT name FROM prefs";
  $result = mysql_query($query);
  $row = mysql_fetch_array($result);
  return $row['name'];
}

function treat($text) {
	$s1 = str_replace("\n\r", "\n", $text);
	return str_replace("\r", "", $s1);
}
