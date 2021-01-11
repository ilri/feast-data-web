<?php
include("con_db.php");
global $db;
$db->debug=1;
$db->setFetchMode(ADODB_FETCH_ASSOC);
error_reporting(E_ALL);
ini_set('display_errors', 0);
  echo "<pre>";
$getData = $db->GetArray("select *from employees");
  print_r($getData);
?>