<?php
include_once 'config.php';
  function GetLocalMySQLConnection()
  {
	 global $dBservername, $dBUsername, $dBPassword, $dBName;
	   try {
       return mysqli_connect($dBservername, $dBUsername, $dBPassword, $dBName);
     } catch (\Exception $e) {

     }
     return false;
   }

 ?>
