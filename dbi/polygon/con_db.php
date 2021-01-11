<?php
include('adodb/adodb.inc.php');
include('adodb/adodb-active-record.inc.php');
include('adodb/tohtml.inc.php');
include('adodb/toexport.inc.php');


   
  include("sysDefs.php"); 
  $db->setFetchMode(ADODB_FETCH_ASSOC);

  require_once('classModules.php');
 $log  = new Log("dblogs-".date('Ymd').".log");
 $rs  = new GetInfo();
/*****************************my functions ******************************/
function rpath($pathstr)
{
  $path = str_replace('\\', '/', $pathstr);
  return $path;
}

    // Get Table Increment ID
  function getID($tblName)
  {
    global $db;
    if (DB_DRIVER == "mysqli") {
      $getData = $db->GetRow("show table status like '$tblName'");
      $curID = $getData["Auto_increment"];
    }
    else
    { 
      $currentID = $db->GetOne("select  IDENT_CURRENT('$tblName')");
      $curID = $currentID == 1 ? 1 : $currentID +1;
    }
   return  $curID;
  }

function path($pathstr)
    {
    $path = str_replace(array('/', '\\'), SEPARATOR, $pathstr);
      return $path;
    }

//Sanitive Inputs and Output 
function cleanInput($input) {
 
  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );
 
    $output = preg_replace($search, '', $input);
    return $output;
  }
function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}

// End Sanitizations
function sendSMS($SendTo,$message)
{
  // Be sure to include the file you've just downloaded
require_once('AfricasTalkingGateway.php');

// Specify your login credentials
$username   = "intellihub";
$apikey     = "e876d62b8da1ceb4f270272631b2dd33784686d88053741a064407cfb27243d1";


  $recipients = implode(',', $SendTo);

$gateway    = new AfricasTalkingGateway($username, $apikey);

try 
{ 
  // Thats it, hit send and we'll take care of the rest. 
  $from = "INTELLIHUB";
  $results = $gateway->sendMessage($recipients, $message,$from);
     
  foreach($results as $result) {
    // status is either "Success" or "error message"
    /*echo " Number: " .$result->number;
    echo " Status: " .$result->status;
    echo " MessageId: " .$result->messageId;
    echo " Cost: "   .$result->cost."\n";*/
     //print_r($result);
  }
  return $results;
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error while sending: ".$e->getMessage();
}

}

function getFieldID($FieldText)
 {
  global $db;
  $getFieldID = $db->GetOne("select FieldID from razcustomfields where FieldText='$FieldText'");
    return $getFieldID;
 }

    function generateUniqueCode($prefix,$current_id,$padding=5){
  $id =  str_pad($current_id, $padding, "0", STR_PAD_LEFT);
  return $prefix.$id;
 }

 function getDescription($itemCode,$itemType)
 {
  global $db;
  $getDescription = $db->GetOne("select ItemDescription from listitems where ItemType='$itemType' and ItemCode='$itemCode'");
  return $getDescription;
 }

function safehtml($s)
{
    $s=str_replace("&", "&amp;", $s);
    $s=str_replace("<", "&lt;", $s);
    $s=str_replace(">", "&gt;", $s);
    $s=str_replace("'", "", $s);
    $s=str_replace("/", "_", $s);
    $s=str_replace("\"", "&quot;", $s);
    return $s;
}

 function spchar($word)
   {
    $arr = array();
   $arr["&AMP;"] = "&amp;";
   $arr["&LT;"] = "&lt;";
   $arr["&GT;"] = "&gt;";
   $arr["&APOS;"] = "&apos;";
   $arr["&QUOT;"] = "&quot;";
   return strtr($word, $arr);
   }

function cleanfile($s)
{
    $s=str_replace("&", "&amp;", $s);
    $s=str_replace(" ", "_", $s);
    $s=str_replace("/", "-", $s);
    $s=str_replace("'", "", $s);
    return $s;
}
function wekapesa($str)
{
    $num= preg_replace('[\D]', '', $str);
    return $num;
}


function pesa($number, $fractional=false) {
    if ($fractional) {
        $number = sprintf('%.2f', $number);
    }
    while (true) {
        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
        if ($replaced != $number) {
            $number = $replaced;
        } else {
            break;
        }
    }
    return "Ksh ".$number;
} 

   
 function generatePassword($length=9, $strength=0) {
  $vowels = 'aeuy';
  $consonants = 'bdghjmnpqrstvz';
  if ($strength & 1) {
    $consonants .= 'BDGHJLMNPQRSTVWXZ';
  }
  if ($strength & 2) {
    $vowels .= "AEUY";
  }
  if ($strength & 4) {
    $consonants .= '23456789';
  }
  if ($strength & 8) {
    $consonants .= '@#$%';
  }
 
  $password = '';
  $alt = time() % 2;
  for ($i = 0; $i < $length; $i++) {
    if ($alt == 1) {
      $password .= $consonants[(rand() % strlen($consonants))];
      $alt = 0;
    } else {
      $password .= $vowels[(rand() % strlen($vowels))];
      $alt = 1;
    }
  }
  return $password;
}

 /******** Get date Difference ****************************/

  // Set timezone
  date_default_timezone_set("UTC");
 
  // Time format is UNIX timestamp or
  // PHP strtotime compatible strings
  function dateDiff($time1, $time2, $precision = 6) {
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
 
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
 
    // Set up intervals and diffs arrays
    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();
 
    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Set default diff to 0
      $diffs[$interval] = 0;
      // Create temp time from time1 and interval
      $ttime = strtotime("+1 " . $interval, $time1);
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
  $time1 = $ttime;
  $diffs[$interval]++;
  // Create new temp time from time1 and interval
  $ttime = strtotime("+1 " . $interval, $time1);
      }
    }
 
    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
  break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
  // Add s if value is not 1
  if ($value != 1) {
    $interval .= "s";
  }
  // Add value and interval to times array
  $times[] = $value . " " . $interval;
  $count++;
      }
    }
 
    // Return string with times
    return implode(", ", $times);
  }
 /******************************************/
 function GenerateWord()
{
    //Get a random word
    $nb=rand(3, 10);
    $w='';
    for($i=1;$i<=$nb;$i++)
        $w.=chr(rand(ord('a'), ord('z')));
    return $w;
}

function GenerateSentence()
{
    //Get a random sentence
    $nb=rand(1, 10);
    $s='';
    for($i=1;$i<=$nb;$i++)
        $s.=GenerateWord().' ';
    return substr($s, 0, -1);
}
//Get File size
function filesize_formatted($size)
{
    //$size = filesize($path);
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

    function dateView($date){
          if ($date != "") {
          $DDate = date('D jS M Y g:i a',strtotime($date));
          }else
          {
            $DDate = "-----";
          }
        return $DDate;
    }
   function isdate($date){
          if ($date != "") {
          $DDate = date('d-m-Y',strtotime($date));
          }else
          {
            $DDate = "";
          }
        return $DDate;
    }
  function istime($time)
  {
  $str =  str_pad(wekapesa($time), 4, "0", STR_PAD_LEFT);
  $DTime  = date("H:i",strtotime($str));
   return $DTime;
  }
// Trim Tab and return
  function trimtab($str)
  {
    $str = str_replace("\t", "", $str);
    $str = str_replace("\r", "", $str);
    return $str;
  }
 //Get Item List from Table
    function getItemList($fieldName,$tableName)
    {
      global $db;
      $getData = $db->Execute("select S_ROWID,$fieldName from $tableName order by $fieldName");
      $html ="<option></option>";
       while (!$getData->EOF) {
         $S_ROWID = $getData->fields[0];
         $columnName = $getData->fields[1];
         $html .= "<option value='$S_ROWID'>$columnName</option>";
         $getData->MoveNext();
       }
       return $html;
    }
  //Get List Item Edit
   function getListItemIPTC($ItemCode,$ItemType,$op)
    {
      global $db;
     
      if($op == "add")
      {
      $html ="<option></option>";
      $getData = $db->Execute("select ItemCode,ItemDescription from listitems where ItemType= '$ItemType' ");
      }else
      {
  $html = "<option value='$ItemCode'>$ItemCode</option>";
  $getData = $db->Execute("select ItemCode,ItemDescription from listitems where ItemType= '$ItemType' and ItemCode<>'$ItemCode'");
      }
       while (!$getData->EOF) {
         $ItemCode = $getData->fields["ItemCode"];
         $ItemDescription = $getData->fields["ItemDescription"];
         $html .= "<option value='$ItemCode'>$ItemCode</option>";
         $getData->MoveNext();
       }
       
       return $html;
    }


    function getItemList_edit($ItemCode,$fieldName,$tableName)
    {
      global $db;
      $columnName = $db->GetOne("select $fieldName from $tableName where S_ROWID='$ItemCode'");
      $getData = $db->Execute("select S_ROWID,$fieldName from $tableName where S_ROWID<>'$ItemCode' order by $fieldName");
      $html ="<option value='$ItemCode'>$columnName</option>";
       while (!$getData->EOF) {
         $S_ROWID = $getData->fields[0];
         $columnName = $getData->fields[1];
         $html .= "<option value='$S_ROWID'>$columnName</option>";
         $getData->MoveNext();
       }
       $html .="<option></option>";
       return $html;
    }

  //Check DataType
    function checkDT($value,$getDataType)
    {
      global $dbName;
      global $db;
     

        if ($value !="") {
          switch ($getDataType) {
           case 'date':
             $value = date('Y-m-d',strtotime($value));
             break;
           case 'datetime':
             $value = date('Y-m-d H:i:s',strtotime($value));
             break;
            case 'timestamp':
             $value = date('Y-m-d H:i:s',strtotime($value));
             break;
          case 'time':
             $value = date('H:i:s',strtotime($value));
             break;
            case 'int':
             $value = wekapesa($value);
             break;
            case 'decimal':
             $value = (float)$value;
             break;
           default:
             $value = $value;
             break;
         }

        }
       return $value;
    } 


    /***********************convert number to words ************************/
   function translateToWords($number) 
{
    $max_size = pow(10,18);
    if (!$number) return "zero";
    if (is_int($number) && $number < abs($max_size)) 
    {            
        switch ($number) 
        {
            // set up some rules for converting digits to words
            case $number < 0:
                $prefix = "negative";
                $suffix = translateToWords(-1*$number);
                $string = $prefix . " " . $suffix;
                break;
            case 1:
                $string = "One";
                break;
            case 2:
                $string = "Two";
                break;
            case 3:
                $string = "Three";
                break;
            case 4: 
                $string = "Four";
                break;
            case 5:
                $string = "Five";
                break;
            case 6:
                $string = "Six";
                break;
            case 7:
                $string = "Seven";
                break;
            case 8:
                $string = "Eight";
                break;
            case 9:
                $string = "Nine";
                break;                
            case 10:
                $string = "Ten";
                break;            
            case 11:
                $string = "Eleven";
                break;            
            case 12:
                $string = "Twelve";
                break;            
            case 13:
                $string = "Thirteen";
                break;            
            // fourteen handled later
            case 15:
                $string = "Fifteen";
                break;            
            case $number < 20:
                $string = translateToWords($number%10);
                // eighteen only has one "t"
                if ($number == 18)
                {
                $suffix = "een";
                } else 
                {
                $suffix = "teen";
                }
                $string .= $suffix;
                break;            
            case 20:
                $string = "Twenty";
                break;            
            case 30:
                $string = "Thirty";
                break;            
            case 40:
                $string = "Forty";
                break;            
            case 50:
                $string = "Fifty";
                break;            
            case 60:
                $string = "Sixty";
                break;            
            case 70:
                $string = "Seventy";
                break;            
            case 80:
                $string = "Eighty";
                break;            
            case 90:
                $string = "Ninety";
                break;                
            case $number < 100:
                $prefix = translateToWords($number-$number%10);
                $suffix = translateToWords($number%10);
                $string = $prefix . "-" . $suffix;
                break;
            // handles all number 100 to 999
            case $number < pow(10,3):                    
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,2)))) . " Hundred";
                if ($number%pow(10,2)) $suffix = " and " . translateToWords($number%pow(10,2));
                $string = $prefix . $suffix;
                break;
            case $number < pow(10,6):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,3)))) . " Thousand";
                if ($number%pow(10,3)) $suffix = translateToWords($number%pow(10,3));
                $string = $prefix . " " . $suffix;
                break;
            case $number < pow(10,9):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,6)))) . " Million";
                if ($number%pow(10,6)) $suffix = translateToWords($number%pow(10,6));
                $string = $prefix . " " . $suffix;
                break;                    
            case $number < pow(10,12):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,9)))) . " Billion";
                if ($number%pow(10,9)) $suffix = translateToWords($number%pow(10,9));
                $string = $prefix . " " . $suffix;    
                break;
            case $number < pow(10,15):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,12)))) . " Trillion";
                if ($number%pow(10,12)) $suffix = translateToWords($number%pow(10,12));
                $string = $prefix . " " . $suffix;    
                break;        
            // Be careful not to pass default formatted numbers in the quadrillions+ into this function
            // Default formatting is float and causes errors
            case $number < pow(10,18):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,15)))) . " quadrillion";
                if ($number%pow(10,15)) $suffix = translateToWords($number%pow(10,15));
                $string = $prefix . " " . $suffix;    
                break;                    
        }
    } else
    {
        echo "ERROR with - $number<br/> Number must be an integer between -" . number_format($max_size, 0, ".", ",") . " and " . number_format($max_size, 0, ".", ",") . " exclussive.";
    }
    return $string;    
}
       /********************************end convert  *******************************************/
       function getDatesFromRange($start, $end){
    $dates = array($start);
    while(end($dates) < $end){
        $dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
    }
    return $dates;
}


  // Delete Record
  function logAction($S_ROWID,$tbl,$user,$action,$LogChanges = null,$ModCode)
  {
    global $db;
    global $rs;
    $ModCode = is_null($ModCode) ? "" : $ModCode;
    switch ($action) {
      case 'Delete':
       $getData = $rs->row($tbl,"S_ROWID='$S_ROWID'");
       $arg = json_encode($getData);
        break;
      case 'PasswordReset':

        $arg = json_encode($LogChanges);;
        break;
      default:
       $arg = json_encode($LogChanges);
        break;
    }
     
  

       $record["OpTable"] = $tbl;
       $record["ModCode"] = $ModCode;
       $record["OpItemID"] = $S_ROWID;
       $record["OpUser"] = $user;
       $record["OpData"] = $arg;
       $record["OpAction"] = $action;
       $record["OpHost"] = $_SERVER['REMOTE_ADDR'];

       if ($action == "Delete") {
        $del = $db->Execute("delete from $tbl where S_ROWID='$S_ROWID'");
       }
         $syncTble = array("BirthRecords","DeathRecords");
         if (in_array($tbl, $syncTble)) {
            $IsVerified = $db->GetOne("select IsVerified from $tbl where S_ROWID='$S_ROWID'");
            if ($IsVerified == 'Y') {
              
               $recsync["AuditID"] = getID("audit_trail");
               $recsync["ItemID"] = $S_ROWID;
               $recsync["ItemTable"] = $tbl;
               $table  = "syncdata";
               $action = "INSERT";
               $db->AutoExecute($table,$recsync,$action);
            }
         }


       $table  = "audit_trail";
       $action = "INSERT";
       $db->AutoExecute($table,$record,$action);

  }
 

  // Function Get Table Structure
  function gettblStructure($tblName)
  {
    global $db;
   $doQuery = $db->GetRow("show create table $tblName");
   $tableStructure = str_replace('`', "", $doQuery[1]);
   return $tableStructure;
  }
 
   // Get List Items 
function GetListItem_edit($ItemCode,$ItemType)
{
global $db;
  $prefix = array("PHTMainCategory","PHTSubCategory");
$ItemDescription = $db->GetOne("select ItemDescription from listitems where ItemType= '$ItemType' and ItemCode='$ItemCode'");
$ItemDescription = in_array($ItemType, $prefix) ? $ItemCode.'-'.$ItemDescription : $ItemDescription;
$html = "<option value='$ItemCode'>$ItemDescription</option>";
$getData = $db->Execute("select ItemCode,ItemDescription from listitems where ItemType= '$ItemType' and ItemCode<>'$ItemCode'");
while (!$getData->EOF) {
$ItemCode = $getData->fields[0];
$ItemDescription = $getData->fields[1];
$ItemDescription = in_array($ItemType, $prefix) ? $ItemCode.'-'.$ItemDescription : $ItemDescription;
$html .= "<option value='$ItemCode'>$ItemDescription</option>";


$getData->MoveNext();
}
$html .="<option></option>";
return $html;
}
// On add List Items
function getListitem_add($ItemType)
 { 
  global $db;
  $prefix = array("PHTMainCategory","PHTSubCategory");
     echo "<option value=''></option>";
    $getdata= $db->Execute("select ItemCode,ItemDescription from listitems where ItemType='$ItemType' order by 1");
            while(!$getdata->EOF)
            {
            $ItemCode = $getdata->fields[0];
            $ItemDescription = $getdata->fields[1];
            $ItemDescription = in_array($ItemType, $prefix) ? $ItemCode.'-'.$ItemDescription : $ItemDescription;
            echo "<option value='$ItemCode'>{$ItemDescription}</option>";   
        $getdata->MoveNext();
            }
 }

 // List Items
 function conJson($assetmetadata)
{
$assetmetadata = str_replace("{", "[", $assetmetadata);
$assetmetadata = str_replace("}", "]", $assetmetadata);
$assetmetadata = str_replace('","', '"],["', $assetmetadata);
$assetmetadata = str_replace('":"', '","', $assetmetadata);
$assetmetadata = '['.$assetmetadata.']';
return $assetmetadata;
}
  // Send Metadata to Razuna
  function sendRazData($api_key,$assetID,$assetType,$DocID)
    {
     global $db;
  $Copyrightowner["KNA"] = "Ministry of Information, Communication and Technology. Government of Kenya";
$Copyrightowner["DFS"] = "Ministry of Sports, Arts and Culture, Government of Kenya";
$Copyrightowner["KBC"] = "Kenya Broadcasting Corporation";

   $getInfo = $db->GetRow("select *from ImageTextFiles where DocID='$DocID'");
        $arg = array_filter($getInfo);
      if (!empty($arg)) {
        $keywords = $getInfo["FileKeywords"];
        $description = $getInfo["FileDescription"];
      }
      else
      {
        $keywords = "no data";
        $description = "no data";
      }
  

  switch ($assetType) {
     case 'doc':
$metadata = array(
   'lang_id_r' => "lang_id_r",
    'file_keywords' => $keywords,
    'file_desc' => $description
    );
         break;
     case 'img':
$metadata = array(
    'lang_id_r' => "1",
    'img_keywords' => $keywords,
    'img_description' => $description,
    'intellectualgenre' => $getInfo["IntellectualGenre"],
    'headline' => $getInfo["HeadLine"],
    'scene' => $getInfo["IPTCScene"],
    'countrycode' => $getInfo["ISOCountry"],
    'ciadrregion' => $getInfo["County"],
    'country' => getDescription($getInfo['ISOCountry'],"Country"),
    'creator' => $getInfo["CRCreator"],
    'authorsposition' => getDescription($getInfo['CRCreatorJobTitle'],"CRCreatorJobTitle"),
    'datecreated' => date('d-m-Y'),
    'location' => $getInfo["SceneLocation"],
    'state' => $getInfo["County"],
    'source' => $getInfo["CRSource"],
    'rights' => "Copyright&copy; ".date('Y')." ".$Copyrightowner[$getInfo['CRSource']].", All Rights Reserved.",
    'captionwriter' => $getInfo["CreatedBy"],
    'ciadrextadr' => "Department of Information, Uchumi House, 5th Flr, Aga Khan Walk, P.O. Box 8053 -00300",
    'credit' => getDescription($getInfo['CRSource'],"CRSource")."/".getDescription($getInfo['CRCreatorJobTitle'],"CRCreatorJobTitle"),
    );
         break;
    case 'vid':
$metadata = array(
   'lang_id_r' => "1",
    'vid_keywords' => $keywords,
    'vid_description' => $description
    );  
         break;
    case 'aud':
$metadata = array(
   'lang_id_r' => "1",
    'aud_keywords' => $keywords,
    'aud_description' => $description
    );  
         break;
     default:
$metadata = array(
   'lang_id_r' => "1"
    ); 
         break;
 }

$assetmetadata = conJson(json_encode($metadata));
$assetmetadata = urlencode($assetmetadata);
$postString = 'method=setmetadata&api_key='.$api_key.'&assetid='.$assetID.'&assettype='.$assetType.'&assetmetadata='.$assetmetadata;
   
$Posturl = 'http://197.248.7.147/razuna/global/api2/asset.cfc';


$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch2, CURLOPT_URL, $Posturl);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $postString);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_VERBOSE, 1);

$response = curl_exec($ch2);

 $rsp = json_decode($response,true);
  return $rsp;
    }

 // Send to Razuna
 function SendtoRaz($DocID)
{
   global $db;
   $FileNameOut = $db->GetOne("select New_FileName from ElementStorage where S_ROWID='$DocID'");

  $post_data['fa']           = "c.apiupload";
  $post_data['api_key']      = "D1F1F7DCF3604AA485EA866EF1EDA145";
  $post_data['destfolderid'] = "A0D54ED374AA4842814EC3BB0F8F76C5";
  $post_data['debug']        = "1";
  $post_data['emailto']      = "nextadmin@next.co.ke";
  $api_key = $post_data['api_key']; 
  // File you want to upload/post.
  $fileName = realpath($FileNameOut);
  // Initialize cURL.
  $url = "http://197.248.7.147/razuna/raz1/dam/";
  $url = $url . '/index.cfm';
   
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
  // Set URL on which you want to post the Form and/or data.
  curl_setopt($ch, CURLOPT_URL, $url);
  // Data+Files to be posted.

  if (function_exists('curl_file_create')) {
    unset($post_data['filedata']);
    $post_data['filedata'] = new CurlFile($fileName, 'file/exgpd', $fileName);
  }


  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
  // Pass TRUE or 1 if you want to wait for the response.
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  // Debug mode: shows up any error encountered during the operation.
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  // Execute the request.
  $response = curl_exec($ch);
  curl_close($ch);

  $array = explode("\n", $response);
  $assetID = $array[4];
  $assetType = $array[5];

  return $response;
}
//End Send To Razuna

//  Razuna Send Custom Fields
function sendCustom($api_key,$assetID,$DocID)
{
  global $db;
  $rst = $db->GetRow("select *from ImageTextFiles where DocID='$DocID'");
  
$Copyrightowner["KNA"] = "Ministry of Information, Communication and Technology. Government of Kenya";
$Copyrightowner["DFS"] = "Ministry of Sports, Arts and Culture, Government of Kenya";
$Copyrightowner["KBC"] = "Kenya Broadcasting Corporation";

$setmetadata[getFieldID('Category')]  = getDescription($rst['MainCategory'],"PHTMainCategory");
$setmetadata[getFieldID('County')]    = $rst['County'];
//$setmetadata[getFieldID('Creator')]       = $rst['CRCreator'];
//$setmetadata[getFieldID('Location')]      = $rst['SceneLocation'];
$setmetadata[getFieldID('Caption')]     = $rst['FileCaption'];
//$setmetadata[getFieldID('Country')]   = getDescription($rst['ISOCountry'],"Country");
$setmetadata[getFieldID('Creator Job Title')]   = getDescription($rst['CRCreatorJobTitle'],"CRCreatorJobTitle");
$setmetadata[getFieldID('Date Received')]     = isdate($rst['DateImgCreated']);
//$setmetadata[getFieldID('Description Writer')]  = $rst['CreatedBy'];
$setmetadata[getFieldID('Digital Source Type')] = getDescription($rst['DigiSourceType'],"DigiSourceType");
$setmetadata[getFieldID('Dimensions Of Asset')] = $rst['Dimensions'];
$setmetadata[getFieldID('Headline')]      = $rst['HeadLine'];
//$setmetadata[getFieldID('Intellectual Genre')]  = getDescription($rst['IntellectualGenre'],"IntellectualGenre");
$setmetadata[getFieldID('Logos And Brands')]  = $rst['Logos'];

$setmetadata[getFieldID('Original File Number')] = $rst['FileName'];
$setmetadata[getFieldID('Published To Public')] = $rst['PublishtoPublic'] == "Y" ? "T":"S";
$setmetadata[getFieldID('Remarks')]       = $rst['Remarks'];
$setmetadata[getFieldID('Source')]  = $rst['CRSource'];
$setmetadata[getFieldID('Sub Category')]    = getDescription($rst['SubCategory'],"PHTSubCategory");
//$setmetadata[getFieldID('Format Of Asset')]   = $rst['CRFormat'];
//$setmetadata[getFieldID('IPTC Scene')]      = $rst['IPTCScene'];
//$setmetadata[getFieldID('Contact Info')] = "Department of Information, Uchumi House, 5th Flr, Aga Khan Walk, P.O. Box 8053 -00300";
//$setmetadata[getFieldID('Copyright Notice')] = "Copyright&copy; ".date('Y')." ".$Copyrightowner[$rst['CRSource']].", All Rights Reserved.";
//$setmetadata[getFieldID('Copyright Owner')] = $Copyrightowner[$rst['CRSource']];
//$setmetadata[getFieldID('Credit Line')] = getDescription($rst['CRSource'],"CRSource")."/".getDescription($rst['CRCreatorJobTitle'],"CRCreatorJobTitle");
$setmetadata[getFieldID('Date Injected')] = date('d-m-Y');
//$setmetadata[getFieldID('ISO Country Code')] = $rst['ISOCountry'];
$setmetadata[getFieldID('Checked By')] = $rst['ModifiedBy']." on ".date('D jS M Y g:i a',strtotime($rst['DateModified']));
$setmetadata[getFieldID('Double Checked')] = 'T';


$jArray = conJson(json_encode($setmetadata));

  $jArray = urlencode($jArray);
 
   
$Posturl = 'http://197.248.7.147/razuna/global/api2/customfield.cfc?method=setfieldvalue&api_key='.$api_key.'&assetid='.$assetID.'&field_values='.$jArray;

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch2, CURLOPT_URL, $Posturl);
//curl_setopt($ch2, CURLOPT_POSTFIELDS, $postString);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_VERBOSE, 1);

$response = curl_exec($ch2);

 $rsp = json_decode($response,true);
 return $rsp;
}
function getConf($confType)
{
  global $db;
  $confValue = $db->GetOne("select confValue from AppConfigs where confType='$confType'");
  return $confValue;
}

  //   Sort Tiff Images
function DotiffImg($DocID)
{
  global $db;
  $img ="";
$filepath = $db->GetOne("select New_FileName from ElementStorage where S_ROWID='$DocID'");
try
{
// Saving every page of a TIFF separately as a JPG thumbnail
$images = new Imagick($filepath); 
$PoolPath = getConf("AssetPath");
 
foreach($images as $i=>$image) {
    // Providing 0 forces thumbnail Image to maintain aspect ratio
    $image->thumbnailImage(668,0);
    $i += 1;
    $imgname = sha1($DocID)."-$i";
    if (!file_exists($PoolPath.'tmp')) {
    mkdir($PoolPath.'tmp', 0777, true);
     chmod($PoolPath.'tmp', 0777);
     }
    $image->writeImage($PoolPath."tmp/".$imgname.".jpg");
     chmod($PoolPath."tmp/".$imgname.".jpg", 777);
    
    $tmp = $PoolPath."tmp/".$imgname.".jpg";
   $insTmp = $db->Execute("insert  into tmpfiles(tmpFile,DocID,tmpindex) values('$tmp','$DocID','$i')");
}
$images->clear();
}
catch(Exception $e)
{
        $img .= $e->getMessage();
}

   //return $PoolPath;
}

function getTiffImg($DocID)
{

  global $db;
  $img  = "";
  $getImg = $db->Execute("select tmpFile from tmpfiles where DocID='$DocID'");
    while (!$getImg->EOF) {
      $imgFile = $getImg->fields["tmpFile"];
      $img .= "<img src='$imgFile' alt='images' ></img>";
      echo $imgFile;
      $getImg->MoveNext();
    }
    return $img;
}

// Unpack the List
function UnpackDataList($UserID,$PoolName,$RecStage)
{
   global $db;
  switch ($RecStage) {
     case 'de':
       $S_Column = "Newlist";
       $fileStage = "Verification";
       break;
     case 'se':
     $S_Column = "ErrorList";
     $fileStage = "ErrorList";
       break;
    case 'vr':
     $S_Column = "Level1Verification";
      $fileStage = "Level1Verification";
       break;
    case 'level1':
    $S_Column = "Level1Verification";
    $fileStage = "Level1Verification";
       break;
    case 'level2':
      $S_Column = "Level2Verification";
      $fileStage = "Level2Verification";
       break;
     case 'level3':
      $S_Column = "Level3Verification";
      $fileStage = "Verified";
       break;
     default:
      
       break;
   }
 
  $getlist = $db->GetOne("select $S_Column from listpool where loginid='$UserID' and StoragePool='$PoolName'");
   if($getlist !="")
   {
    $retrieve = explode(',',$getlist);
    
      $DocID = $retrieve[0];
      $updateOutPut = $db->Execute("update ElementStorage set FileStage='$fileStage' where S_ROWID='$DocID'");
    
    array_shift($retrieve);
    $comb = implode(',', $retrieve);
    $UpdateListPool = $db->Execute("update listpool set $S_Column='$comb' where loginid='$UserID' and StoragePool='$PoolName'");
   }
}
//End Unpack List


  function getDataType($tableName,$column)
  {
    global $db;
    $getInfo = $db->GetRow("select *from tbl_columns where TblName='$tableName' and FieldName='$column'");
    return $getInfo;
  }

  // Get Field Data Types
  function MetaType($tableName)
  {
    global $db;
    $MetaColumns = $db->MetaColumns($tableName);
    foreach ($MetaColumns as $key => $val) {
       $MetaType[$val->name] = $val->type;
    }
    return $MetaType;
  }

 // Do Audit trail values 

  function DoDateConvert(&$value, $key, $MetaTypes) 
  { 
    if ($value != "") {
        $type = $MetaTypes[$key];
    switch ($type) {
      case 'date':
        $value = date("D jS M Y",strtotime($value));
        break;

      case 'datetime':
        $value =  date('D jS M Y h:i A',strtotime($value));
        break;
      case 'timestamp':
        $value =  date('D jS M Y h:i A',strtotime($value));
        break;

      default:
        $value = $value;
        break;
    }
    }
    else
    {
       $value = $value;
    }

  } 

    function doAuditLog($postVals,$S_ROWID,$tableName)
  {

    global $db;
    $LogChanges = array();
  $record = array();

  $MetaType = MetaType($tableName);
   // Check if they Are Array Items
    foreach ($postVals as $key => $value) {
    $postVals[$key] = $value;
      if (is_array(($value))) {
       
       $postVals[$key] = json_encode($value);
      }
    }
    unset($postVals["btnUpdateRecord"]);
    unset($postVals["btnSaveRecord"]);
    unset($postVals["btnUpdateRec"]);
    // End Checking
   foreach ($postVals as $postKey => $postValue) {
        $postFld[] = $postKey;
       }
    $postFields = implode(',', $postFld);
    $cols = explode(',', $postFields);
     $getOData = $db->GetRow("select $postFields from $tableName where S_ROWID='$S_ROWID'");
        
       
      foreach ($postVals as $Pkey => $Pvalue) {
        $type = $MetaType[$Pkey];
          

          switch ($type) {
            case 'date':
            $Pvalue = $Pvalue !="" ? date('Y-m-d',strtotime($Pvalue)) : "";
              break;
            case 'datetime':
            $Pvalue = $Pvalue != "" ? date('Y-m-d H:i:s',strtotime($Pvalue)) : "";
              break;
            case 'timestamp':
             $Pvalue = $Pvalue != "" ? date('Y-m-d H:i:s',strtotime($Pvalue)) : "";
              break;
            
            default:
              $Pvalue = $Pvalue;
              break;
          }
           
          $OldValue = $getOData[$Pkey];
        
        if ($Pvalue != $OldValue) {
            $record[$Pkey] = trim(checkDT($Pvalue,$type));
          
          $logInfo = array();
          $logInfo["Field"] = $Pkey;
          $logInfo["Ovalue"] = $OldValue;
          $logInfo["Nvalue"] = $Pvalue;
          $LogChanges[] = $logInfo;
        }
      }
    
  $results = array();
  $results[] = $record;
  $results[] = $LogChanges;
   return $results;
  }

  // Get Full path Url
  function full_path()
{
    $s = &$_SERVER;
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    $uri = $protocol . '://' . $host . $s['REQUEST_URI'];
    $segments = explode('?', $uri, 2);
    $url = $segments[1];
    return $url;
}



?>
