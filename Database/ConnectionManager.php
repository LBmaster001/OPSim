<?php
include_once $_SERVER['DOCUMENT_ROOT']. '/config.php';
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
