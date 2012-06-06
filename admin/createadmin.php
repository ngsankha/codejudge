<?php

function randomAlphaNum($length){

    $rangeMin = pow(36, $length-1); //smallest number to give length digits in base 36
    $rangeMax = pow(36, $length)-1; //largest number to give length digits in base 36
    $base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number
    $newRand = base_convert($base10Rand, 10, 36); //convert it
   
    return $newRand; //spit it out

}

$salt=randomAlphaNum(5);
$pass="admin";
$email="sankha93@gmail.com";
mysql_connect("localhost","sankha93","abcd1993");
mysql_select_db("codejudge") or die('!');
$hash=crypt($pass,$salt);
$sql="INSERT INTO `users` ( `username` , `salt` , `hash` , `email` ) VALUES ('$pass', '$salt', '$hash', '$email')";
mysql_query($sql);
mysql_close();
