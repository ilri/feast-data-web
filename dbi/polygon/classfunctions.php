<?php
include("con_db.php");
global $db;
   //$db->debug=1;
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('allow_url_fopen', '1');
ini_set('display_errors', 1);
  

if (isset($_POST['ProFileimage'])) {
  $data = $_POST['ProFileimage'];
  $PoolID = $_POST['PoolID'];

list($tableName,$S_ROWID) = explode(':', $PoolID);
list($type, $data) = explode(',', $data);

  error_reporting(E_ALL ^ E_DEPRECATED);
$data = base64_decode($data);
$rs->makeDir(DIR_ROOT,"/assets/profilepics/");
$imageName = $rs->encode($PoolID).'.png';
$getPath = $db->GetOne("select ProfileImg from $tableName where S_ROWID='$S_ROWID'");
   if ($getPath !="") {
     unlink("../../".$getPath);
   }


$upload_dir = DIR_ROOT."/assets/profilepics/";
file_put_contents($upload_dir.$imageName, $data);
$ImgPath   = "assets/profilepics/".$imageName;
$UpdateImg = $db->Execute("update $tableName set ProfileImg='$ImgPath' where S_ROWID='$S_ROWID' ");
}


if (isset($_POST['btnUpdateRecord'])) {
  $tableName = safehtml($_POST['btnUpdateRecord']);
  array_pop($_POST);
     $S_ROWID = $_POST['S_ROWID'];
     foreach ($_POST as $key => $value) {
       $record[$key] = safehtml($html);
     }

       $table  = $tableName;
       $action = "UPDATE";
       $db->AutoExecute($table,$record,$action,$criteria);
}


if (isset($_POST['convertFDate'])) {
  $selDate    = $_POST["convertFDate"];
  $CurDocType = $_POST["CurDocType"];
  $DocSource  = $_POST['DocSource'];
  $convertedDate = "";
  if ($selDate !="") {
    $convertedDate = date('d.m.y', strtotime($selDate));
  }
  else
  {
    $convertedDate = $CurDocType;
  }
  
  $DocumentTitle = $DocSource == "Assembly" ? "CAK ".$CurDocType." ".$convertedDate : $convertedDate;


  echo $DocumentTitle;
}


    
    
?>