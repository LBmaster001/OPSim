<?php
include_once $_SERVER['DOCUMENT_ROOT']. '/config.php';
$conn = GetDBConnection();

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

function GetDBConnection()
{
	global $dBservername, $dBUsername, $dBPassword, $dBName;
	try {
		$conn = mysqli_connect($dBServername, $dBUsername, $dBPassword, $dBName);
	} catch (\Exception $e) {
		$conn = false;
	}

	return $conn;
}
