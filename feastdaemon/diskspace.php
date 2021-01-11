#!/usr/bin/php
<?php
  include("con_db.php");
  global $db;
  //$db->debug=1;
    $df = disk_free_space(__DIR__);
    $tableName = "system_setting";
    
      $record["setting"]  = "avaiable_disk_space";
      $record["value"]  = filesize_formatted($df);
      $record["created_by"]  = "sysadmin";
      $record["modified_by"]  = "sysadmin";
      

      $exist = $db->GetOne("select id from $tableName where setting='avaiable_disk_space'");
        if ($exist == "") {
            $table  = $tableName;
       		$action = "INSERT";
           $db->AutoExecute($table,$record,$action);
        }
        else
        {
        	$criteria = "id = $exist";
        	$table  = $tableName;
       		$action = "UPDATE";
           $db->AutoExecute($table,$record,$action,$criteria);
        }
?>