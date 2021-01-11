<?php  
class Log {
  private $handle;

  public function __construct($filename) {
    //$this->handle = fopen(DB_LOGS . $filename, 'a');
  }

  public function write($message) {
    $fd = $this->handle;
    if ($fd) {
      fwrite($fd, print_r($message, true) . ";\n");
    fclose($fd);
    }
    
  }

  public function makesql($table,$record,$action,$criteria = null){
    $fields = "";
$output = "";
       if ($action == "INSERT" || $action == "REPLACE") {
          $fields = "$action INTO $table (";
        $sep = "";
        // grab each field name
        foreach($record as $col => $val){
            $fields .= $sep . "$col";
            $sep = ", ";
        }
        $fields .= ") VALUES";
        $output .= $fields;
           
            $sep = "";
            $output .= $sep . "(";
            foreach($record as $col => $val){
                // add slashes to field content
                $val = addslashes($val);
                // replace stuff that needs replacing
                $search = array("\'", "\n", "\r");
                $replace = array("''", "\\n", "\\r");
                $val = str_replace($search, $replace, $val);
                $output .= $sep . "'$val'";
                $sep = ", ";
            }
            // terminate row data
            $output .= ")";
       }  
       else
       {
        $fields = "UPDATE $table SET ";
        $sep = "";
        $output .= $fields;
           
            // grab table data
            $sep = "";
            $output .= $sep . " ";
            foreach($record as $col => $val){
                // add slashes to field content
                $val = addslashes($val);
                // replace stuff that needs replacing
                $search = array("\'", "\n", "\r");
                $replace = array("''", "\\n", "\\r");
                $val = str_replace($search, $replace, $val);
                $output .= $sep . "$col='$val'";
                $sep = ", ";
            }
            // terminate row data
            $output .= $criteria != "" ? " where $criteria" : "";
       }
     return $output;
  }


}
// End Log Class

/**
* Class to get Row info and List Items 
*/
class GetInfo
{

// Get Week Start and End Date
  function getWeekSten($week, $year) {
  $dto = new DateTime();
  $dto->setISODate($year, $week);
  $ret['week_start'] = $dto->format('Y-m-d');
  $dto->modify('+6 days');
  $ret['week_end'] = $dto->format('Y-m-d');
  return $ret;
}


function weeks_in_month($month, $year) {
 $start = mktime(0, 0, 0, $month, 1, $year);
 $end = mktime(0, 0, 0, $month, date('t', $start), $year);
 $start_week = date('W', $start);
 $end_week = date('W', $end);
   $Wlist  = array();
    
    //year has 52 weeks
            $weeksInYear = 52;
            //but if leap year, it has 53 weeks
            if($year % 4 == 0) {
                $weeksInYear = 53;
            }
           if ($month == 12) {
             $WeeksInmonth = (($weeksInYear + $end_week) - $start_week) ;
             
             $end_week = $weeksInYear;
           }

    for ($i= $start_week; $i <= $end_week; $i++) { 
      $Wlist[] = $i;
    }
   return $Wlist;
}

function list_week_days($year, $month) {
    $MonthWeek = array();
    $WeekNumList = $this->weeks_in_month($month, $year);
   $lastkey = count($WeekNumList)-1;
   foreach ($WeekNumList as $key => $WeekNum) {
     $WdRange = $this->getWeekSten($WeekNum, $year);
       if ($key == 0) {
        $first_month_day =  new DateTime("first day of $year-$month");
         $firstMDay = $first_month_day->format("Y-m-d");
        $WdRange["week_start"] =  $firstMDay;
       }

       if ($lastkey == $key) {
         $last_month_day =  new DateTime("last day of $year-$month");
         $lastMDay = $last_month_day->format("Y-m-d");
        $WdRange["week_end"] = $lastMDay;
       }
      $MonthWeek[] = $WdRange;
   }

   return $MonthWeek;
}

  // Get Columns
  function GetColInfo($modInfo)
  {
  global $db;
    $ModuleCode = $modInfo["ModuleCode"];
    $TableName   = $modInfo["TableName"]; 
    $MetaColumns = $db->MetaColumns($TableName);
    foreach ($MetaColumns as $key => $val2) {
       $MetaType[$val2->name] = $val2->type;
    }

$getCols = $db->GetArray("select FieldName,DisplayName from dh_listview where ModuleCode='$ModuleCode'  and ListType='Main' and TableName='$TableName' order by DisplayOrder asc");
    
   $OrderClm = ""; 
    $columns["columns"] = array();
    $columns["columns"][0]["title"] = "Actions";
    $columns["columns"][0]["className"] = "S_ROWID";
    foreach ($getCols as $key => $val) {
      $type = $MetaType[$val[0]];
      $clmIndex = $key+1;
      $clmName = $val[1];
      $OrderClm .= "<option value='$clmIndex'>$clmName</option>";
      $columns["columns"][$key+1]["title"] = $val[1];
      $columns["columns"][$key+1]["className"] = $val[0];
    }
  $response = array();
  $response["Deflist"] = json_encode($columns); 
  $response["ColCount"] = count($getCols)+1;
  $response["OrderClm"] = $OrderClm;
  return $response;
  }



  function getUserAction($userID,$ModCode,$RoleType)
  {
    global $db;
    $UserProfiles = $this->UserProfile($userID,$RoleType);
    $Profilelist = implode(',', $UserProfiles);
    $Profilelist = $Profilelist != "" ? $Profilelist : 0;
    $getData = $db->Execute("select  ModOperation,IsAllowed from vw_profilepermissions where ProfileID in ($Profilelist) and ModCode='$ModCode'  and ApplicationName not in ('System','User Profile') order by IsAllowed");
    $list = array();
    while (!$getData->EOF) {
      $ModAction = $getData->fields["ModOperation"];
      $list[$ModAction] = $getData->fields["IsAllowed"];
      $getData->MoveNext();
    }
    $modActions = $this->getModAction($ModCode);
    $ModDiff = array_diff_key($modActions,$list);
    $result2 = array_merge($list, $ModDiff);
    return $result2;
  }


  function getModAction($ModCode)
  {
    global $db;
    $getModAction = $db->Execute("select ItemDescription from listitems where ItemType='ModActions' and ItemCode='$ModCode'");
    $list = array();
    while (!$getModAction->EOF) {
      $list[$getModAction->fields["ItemDescription"]] = 0;
      $getModAction->MoveNext();
    }
    return $list;
  }



 function getUserMenuList($userID,$RoleType)
 {
  global $db;
$UserProfiles = $this->UserProfile($userID,$RoleType);
$Profilelist = implode(',', $UserProfiles);
$Profilelist = $Profilelist != "" ? $Profilelist : 0;
//$cols = $this->getCols("vw_profilepermissions");
$getPerms = $db->Execute("select *from vw_profilepermissions where ProfileID in ($Profilelist) and ModOperation='View' and IsAllowed=1 and ApplicationName not in ('SystemApps','UserProfile') order by AppDisplayOrder,ModDisplayOrder");
  $htmlist = array();
while (!$getPerms->EOF) {
   
          $rst = $getPerms->fields;
        
      $AppCode   = $rst["AppS_ROWID"];
      $ModCode   = $rst["ModCode"];
      $htmlist[$AppCode][$ModCode] = $rst;
      
  $getPerms->MoveNext();
}
  return $htmlist;
 } 

function UserProfile($UserID,$RoleType)
{
  global $db;
  $getdata = $db->Execute("select distinct ItemDescription as ProfileID,dp.ProfileName from listitems l inner join dh_userprofiles dp on l.ItemDescription=dp.S_ROWID where ItemType='RoleProfile' and ItemCode in (select ItemCode from listitems where ItemType='$RoleType' and ItemDescription='$UserID') ");
   $list = array();
  while (!$getdata->EOF) {
    $ProfileID = $getdata->fields["ProfileID"];
    $ProfileName = $getdata->fields["ProfileName"];
    $list[$ProfileName] = $ProfileID;
    $getdata->MoveNext();
  }
  return $list;
}

  function getDataTblView($modCode)
  {
    global $db;
    $modInfo    = $this->row("dh_modules","S_ROWID = '$modCode'");
 
 $ModuleCode = $modInfo["ModuleCode"];
$tableName  = $modInfo["TableName"];
$MetaColumns = $db->MetaColumns($tableName);
    foreach ($MetaColumns as $key => $val2) {
       $MetaType[$val2->name] = $val2->type;
    }

$getCols = $db->GetArray("select FieldName,DisplayName from dh_listview where ModuleCode='$ModuleCode' and ListType='Main' and TableName='$tableName' order by DisplayOrder asc");
  $Viewcols[] = "S_ROWID";
  foreach ($getCols as $key => $val) {
    $Viewcols[] = $val["FieldName"];
  }
   $final = array();
   $final["ModInfo"] = $modInfo;
   $final["ViewCols"] = $Viewcols;
  return $final;
  }

  // IF table is View or Not
  function IsView($TableName)
  {
    global $db;
    if (DB_DRIVER == "mysqli") {
      $getView = $db->GetArray("select TABLE_NAME FROM information_schema.views where TABLE_SCHEMA=(SELECT DATABASE())");
    }
    else
    {
      $getView = $db->GetArray("SELECT  name FROM  sys.views WHERE   type = 'V'");
    }
    $list = array();
    for ($i=0; $i < count($getView); $i++) { 
      $list[] = $getView[$i]["TABLE_NAME"];
    }
    return in_array($TableName, $list) ? true : false;
  }
 //  Get Menu Buttons
  function formAction($ActionName)
  {
    global $db;
    $getActInfo = $this->row("menuactions","ActionName='$ActionName'");
    $html = "";
    $MenuType    = $getActInfo["MenuType"];
    $attributes  = $getActInfo["ActionAttributes"];
    $DisplayName = $getActInfo["DisplayName"];
    $class = $getActInfo["ActionClass"];
    $class = $class != "" ? "class='$class'" : "";
    $IconRef = $getActInfo["ActionIconRef"];
    $IconRef = $IconRef !="" ? "<i class='$IconRef'></i>" : "";
    $ToolTip = $getActInfo["ActionToolTip"];
    $ToolTip = $ToolTip !="" ? "title='$ToolTip'" : "";

    $getmItems = $db->Execute("select ActionName,ActionAttributes,DisplayName,ActionClass,ActionToolTip,ActionIconRef,DisplayOrder from menuactions where ParentMenu='$ActionName' order by Displayorder asc");
    $mItem = "";
        while (!$getmItems->EOF) {
          $attributes  = $getmItems->fields[1];
    $DisplayName = $getmItems->fields[2];
    $class       = $getmItems->fields[3];
    $class  = $class != "" ? "class='$class'" : "";
    $IconRef = $getmItems->fields[4];
    $IconRef = $IconRef !="" ? "<i class='$IconRef'></i>" : "";
    $ToolTip = $getmItems->fields[5];
    $ToolTip = $ToolTip !="" ? "title='$ToolTip'" : "";
    $mItem .= "<li><a $class $attributes $ToolTip>$IconRef  $DisplayName</a></li>";
          $getmItems->MoveNext();
        }


    switch ($MenuType) {
      case 'FormButton':
       $html = "<button type='submit' name='$ActionName' id='$ActionName' $attributes $class $ToolTip>$IconRef $DisplayName</button>";
        break;
      case 'IconButton':
          $html = "<button type='button' name='$ActionName' id='$ActionName' $attributes $class $ToolTip>$IconRef $DisplayName</button>";
        break;
      case 'ButtonDropDown':

         $html .= " <div class='row' id='divActionbtn'>";
         $html .= "<span><div class='btn-group'><button type='button' class='btn btn-info'>Action</button>";
  $html .= "<button type='button' class='btn btn-info dropdown-toggle' data-toggle='dropdown'>";
    $html .= "<span class='caret'></span>";
    $html .= "<span class='sr-only'>Toggle Dropdown</span></button>";
  $html .= "<ul class='dropdown-menu' role='menu'>";
    $html .= $mItem;
   $html .= "</ul></div></span></div>";
        break;
      case 'Pop-Up':
        $html .= "<div class='btn-group' id='$ActionName'>";
        $html .= "<a class='dropdown-toggle' data-toggle='dropdown' $ToolTip><i class='fa fa-tasks fa-lg'> $DisplayName</i></a>";
          $html .= "<span class='sr-only'>Toggle Dropdown</span></button>";
          $html .= "<ul class='dropdown-menu' role='menu'>";
           $html .= $mItem;
        $html .= "</ul></div>";
        break;
      default:
        $html = "";
        break;
    }

    return $html;
  }
  // Log File Access
function logFileAction($SessionID,$DocID,$CreatedBy,$LogAction,$Reason = null)
{
  global $db;

   if (isset($_POST['Reason'])) {
     $record["Reason"] = $Reason;
   }
  $record["SessionID"]   = $SessionID;
  $record["DocID"]       = $DocID;
  $record["CreatedBy"]   = $CreatedBy;
  $record["LogAction"]   = $LogAction;
  $record["AccessIP"]    = $_SERVER['REMOTE_ADDR'];
  $record["AccessAgent"] = $_SERVER['HTTP_USER_AGENT'];

    $table  = "fileaccesslog";
    $action = "INSERT";
    $db->AutoExecute($table,$record,$action);
}

   function colors($colorCode = null)
   {
    global $db;
    $colorCode = is_null($colorCode) ? "ItemCode,ItemDescription" : "ItemDescription,ItemCode";
    $getColors = $db->GetArray("select $colorCode from listitems where ItemType='ColorPallete'");
 $colors = array();
 foreach ($getColors as $key => $val) {
   
    $colors[$val[1]] = trim($val[0]);
 }
   return $colors;
   }

// Start File Path
    function makeDir($PoolPath,$path)
{
  $flipped = explode("/",$path);
  foreach ($flipped as $key => $folder) {
  $PoolPath = $PoolPath."/".$folder;
  if (!file_exists($PoolPath)) {
     mkdir($PoolPath, 0777, true);
     chmod($PoolPath, 0777);
     }
}
}

 function getFilePath($DocID)
 {
  if ($DocID <100) {
  
  $path = array();
  $path[0] = "000";
  $path[1] = "000";
  $path[2] = "000";
  $path[3] = "00000";
 }
 else
 {
  
  $ExDocID = substr($DocID, 0, -2);
  $path = array();
  $path[0] = "000";
  $path[1] = "000";
  $path[2] = "000";
  $path[3] = "00000";

  $DPath = array();
  $str  = number_format($ExDocID, 0, '', ",");
  $DPath = explode(',', $str);
  $FPath = array_reverse($DPath);

  foreach ($FPath as $key => $value) {
  $path[$key] = str_pad($value, 3, "0", STR_PAD_LEFT);
  }
  }

  $flipped = array_reverse($path);
  $FilePath = implode("/",  $flipped)."/";
  return $FilePath;
 }
  // End FilePath

  //  Get Module URl
  function Modurl($ModuleCode,$cid = null)
  {
    global $db;
    $modInfo = $this->row("dh_modules","ModuleCode='$ModuleCode'");
    $modID = $modInfo["S_ROWID"];
    $AppCode = $modInfo["AppName"];
    $appID = $db->GetOne("select S_ROWID from dh_applications where AppCode='$AppCode'");
    $rand = md5(mt_rand());
    $cidlink = is_null($cid) ? "" : "&cid=$cid";
    $url = "?app=$appID&mod=$modID&view=edit$cidlink&ptype=temp&sk=$rand";
    return $url;
  }

  //County Info 
   function GetCounty($CountyCode,$op,$order = null)
    {
      global $db;
      $order = is_null($order) ? "CountyCode" : $order;
      if ($op != "add") {
        $CountyName = $db->GetOne("select CountyName from tblcounties where  CountyCode='$CountyCode'");
        $html = "<option value='$CountyCode'>$CountyName</option>";
        $where = " where  CountyCode<>'$CountyCode'";
      }
      else
      {
        $where = "";
        $html  = "<option value=''></option>";
      }
      $getdata = $db->Execute("select CountyCode,CountyName from tblcounties $where order by $order");
         
            while(!$getdata->EOF)
            {
            $CountyCode = $getdata->fields[0];
            $CountyName = $getdata->fields[1];
            $html .= "<option value='$CountyCode'>{$CountyName}</option>";   
        $getdata->MoveNext();
            }
        if ($op != "add") {
          $html  .= "<option value=''></option>";
        }
        return $html;
    }

      function GetConst($County,$op,$order = null,$ConstCode = null)
    {
      global $db;
      $order = is_null($order) ? "ConstCode" : $order;
      if ($op != "add") {
        $ConstName = $db->GetOne("select ConstName from tblconstituency2012 where  ConstCode='$ConstCode' and County='$County'");
        $html = "<option value='$ConstCode'>$ConstName</option>";
        $where = " where  ConstCode<>'$ConstCode' and County='$County'";
      }
      else
      {
        $where = "where County='$County'";
        $html  = "<option value=''></option>";
      }
      $getdata = $db->Execute("select ConstCode,ConstName from tblconstituency2012 $where order by $order");
         
            while(!$getdata->EOF)
            {
            $ConstCode = $getdata->fields[0];
            $ConstName = $getdata->fields[1];
            $html .= "<option value='$ConstCode'>{$ConstName}</option>";   
        $getdata->MoveNext();
            }
        if ($op != "add") {
          $html  .= "<option value=''></option>";
        }
        return $html;
    }

     function GetWards($ConstCode2012,$op,$order = null,$WardCode2012 = null)
    {
      global $db;
      $order = is_null($order) ? "WardCode2012" : $order;
      if ($op != "add") {
        $WardName2012 = $db->GetOne("select `WardName2012` from tblwards where  `WardCode-2012`='$WardCode2012' and `ConstCode2012`='$ConstCode2012'");
        $html = "<option value='$WardCode2012'>$WardName2012</option>";
        $where = " where  `WardCode2012`<>'$WardCode2012' and `ConstCode2012`='$ConstCode2012'";
      }
      else
      {
        $where = "where `ConstCode2012`='$ConstCode2012' and `WardCode2012`<>'$WardCode2012'";
        $html  = "<option value=''></option>";
      }
      $getdata = $db->Execute("select `WardCode2012`,`WardName2012` from tblwards $where order by $order");
         
            while(!$getdata->EOF)
            {
            $WardCode2012 = $getdata->fields[0];
            $WardName2012 = $getdata->fields[1];
            $html .= "<option value='$WardCode2012'>{$WardName2012}</option>";   
        $getdata->MoveNext();
            }
        if ($op != "add") {
          $html  .= "<option value=''></option>";
        }
        return $html;
    }
  // Key Generator
   function generateUniqueCode($prefix,$current_id,$padding=5){
  $id =  str_pad($current_id, $padding, "0", STR_PAD_LEFT);
  return $prefix.$id;
 }
  function random_num($size) {
  $alpha_key = '';
  $alpha_key2 = '';
  $keys = range('A', 'Z');
  

  for ($i = 0; $i < 2; $i++) {
    $alpha_key .= $keys[array_rand($keys)];
  }

  for ($i=0; $i < 1 ; $i++) { 
    $alpha_key2 .= $keys[array_rand($keys)];
  }

  $length = $size - 2;

  $key = '';
  $keys = range(0, 9);

  for ($i = 0; $i < $length; $i++) {
    $key .= $keys[array_rand($keys)];
  }

  return $alpha_key . $key.$alpha_key2;
}

function key_generator()
{
  global $db;
  $tableName = "aspirantregistration";
  $attempts = 0;
  do {
    $key = $this->random_num(9);
    $attempts +=1;
    if ($attempts == 3 ) {
        $key = $this->generateUniqueCode("BB",getID($tableName),8);
        break;
    }
  } while ($db->GetOne("select S_ROWID from $tableName where RegCode='$key'") != "");

  return $key;
}

  // Encode
  var $skey = "God1st0987654321"; // you can change it
  
    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value){ 
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }

    public function decode($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
  // End Decode
     
     function GetListItems($ItemCode,$ItemType,$op,$order = null,$exclude = null)
    {
      global $db;
      $order = is_null($order) ? "ItemCode" : $order;
      $exclude = is_null($exclude) ? array() : $exclude;
      if ($op != "add") {
        $ItemDescription = $db->GetOne("select ItemDescription from listitems where ItemType= '$ItemType' and ItemCode='$ItemCode'");
        $html = "<option value='$ItemCode'>$ItemDescription</option>";
        $where = " where ItemType= '$ItemType' and ItemCode<>'$ItemCode'";
      }
      else
      {
        $ItemExclude = "";
        $arg =  array_filter($exclude);
        if (!empty($arg)) {
          $list        = "'" .implode("','", $exclude)."'";
          $ItemExclude = " and ItemCode not in ($list)";
        }
        $where = " where ItemType='$ItemType'  $ItemExclude";
        $html  = "<option value=''></option>";
      }
      $getdata = $db->Execute("select ItemCode,ItemDescription from listitems $where order by $order");
         
            while(!$getdata->EOF)
            {
            $ItemCode = $getdata->fields["ItemCode"];
            $ItemDescription = $getdata->fields["ItemDescription"];
            $html .= "<option value='$ItemCode'>{$ItemDescription}</option>";   
        $getdata->MoveNext();
            }
        if ($op != "add") {
          $html  .= "<option value=''></option>";
        }
        return $html;
    }
  //  Get Count for Comments and Attachements;
   function getCount($AssetID,$tableName,$Ptype)
   {
     global $db;
   $StoragePool = $AssetID."-".$tableName;
   $where  = $Ptype == "Doc" ? " elementstorage where StoragePool='$StoragePool' " : " dhcomments where AssetID='$AssetID' and TableName='$tableName' ";
   $getCount = $db->GetOne("select count(*) from $where ");
   
  $ItemCount = $getCount != 0 ? " <small class='badge pull-left bg-yellow'> $getCount </small> " : "";

    return $ItemCount;
   }
   
 //  Get Columns Indexes

  //  Create Thump Nails for Tiff Image
function DotiffImg($DocID)
{
  global $db;
  
  $img ="";
$filepath = $db->GetOne("select New_FileName from elementstorage where S_ROWID='$DocID'");

try
{
// Saving every page of a TIFF separately as a JPG thumbnail
$images = new Imagick($filepath); 
$PoolPath = $this->getConf("AssetPath","AssetPath");


if ($this->endsWith($PoolPath,'/') == false) 
{
   $PoolPath = $PoolPath."/";
} 

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
     chmod($PoolPath."tmp/".$imgname.".jpg", 0777);
    
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
  
  function endsWith($FullStr, $needle)
    {
        $StrLen = strlen($needle);
        $FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
        return $FullStrEnd == $needle;
    }
    
  // Check Internet Connection
   function IsConnected()
   {
    $ip = gethostbyname('www.google.com');
    if($ip != 'www.google.com') {
         $status = "Connected";
    } else {
       $status = "NotConnected";
    }

    return $status;
   }
   // Get Mime Type 
  function MimeType($ext)
  {
    $fileInfo   = $this->row("filetypes"," TYPE_ID = '$ext'");
       $arg = array_filter($fileInfo);
       if (!empty($arg)) {
          $mime_type = $fileInfo["TYPE_MIMECONTENT"]."/".$fileInfo["TYPE_MIMESUBCONTENT"];
       }
       else
       {
        $mime_type="application/force-download";
       }
       return $mime_type;
  }

    // Format Size
 function format_size($size) {
   $units = explode(' ','B KB MB GB TB PB');
        $mod = 1024;

        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }

        $endIndex = strpos($size, ".")+3;

        return substr( $size, 0, $endIndex).' '.$units[$i];
    }
  
  function row($tableName,$criteria)
  {
    global $db;
    $criteria = $criteria == "" ? "" : " where $criteria";
    $rst = $db->GetRow("select *from $tableName  $criteria");
    $cols = $db->MetaColumnNames($tableName,true);
        $arg = array_filter($rst);
        if (empty($arg)) {
            foreach ($cols as $key => $value) {
          $rst[$value] = ""; 
          }
        }
      
    return $rst;
  }

  // Get Users and Info in Group
  function GUserInfo($alertlist = array(),$colmn)
  {
    $list = array();
    foreach ($alertlist as $key => $val) {
       if ($val[$colmn] != "") {
          if ($colmn == "Phone") {
            $str = $val[$colmn];
            $str = str_replace(' ', '', $str);
            $str = str_replace('-', '', $str);
              if (strlen($str) == 13) {
                $list[$key] = $str;
              }
          }
            else
         {
          $list[$key] = $val[$colmn];
         }
       }

     }
     return $list;
  }

   function UserInfo($loginid,$column)
   {
    $getUInfo = $this->row("users","loginid = '$loginid'");
    return $getUInfo[$column];
   }
   
  function getGroupUsers($group)
   {
    global $db;
     $userList = array();
     $getGUsers = $db->Execute("select ItemDescription from listitems where ItemType='RoleUser' and ItemCode='$group'");
    while (!$getGUsers->EOF) {
     $UserID = $getGUsers->fields["ItemDescription"];
     $getUInfo = $this->row("vw_userlist","loginid = '$UserID'");
     $userList[$UserID] = $getUInfo;
     $getGUsers->MoveNext();
    }
    return $userList;
   }

   function RoleUsers($group)
   {
    global $db;
     $userList = array();
     $getGUsers = $db->Execute("select ItemDescription from listitems where ItemType='RoleUser' and ItemCode='$group'");
    while (!$getGUsers->EOF) {
     $UserID = $getGUsers->fields["ItemDescription"];
     $userList[] = $UserID;
     $getGUsers->MoveNext();
    }
    return $userList;
   }

  function getGroups($groupCode)
   {
 

      global $db;
      $groupName = $db->GetOne("select GroupName from dh_usergroups where GroupCode='$groupCode'");
      $getData = $db->Execute("select GroupCode,GroupName from dh_usergroups where GroupCode<>'$groupCode' order by GroupCode asc");
      $html ="<option value='$groupCode'>$groupName</option>";
       while (!$getData->EOF) {
         $groupCode = $getData->fields["GroupCode"];
         $groupName = $getData->fields["GroupName"];
         $html .= "<option value='$groupCode'>$groupName</option>";
         $getData->MoveNext();
       }
       $html .="<option value='Null'>None</option>";
       return $html;

   }

  function GetOne($tableName,$colomn,$criteria = null)
  {
    global $db;
    $criteria = is_null($criteria) ? "" : " where $criteria";
    $getData = $db->GetOne("select $colomn from $tableName  $criteria");
    return $getData;
  }
   


  function SetValues($POST,$ItemID,$ItemTable,$user,$ModCode = null)
  {  
    global $db;
     $ModCode = is_null($ModCode) ? "" : $ModCode;
       $auditLog = doAuditLog($POST,$ItemID,$ItemTable);
       $record     = $auditLog[0];
       $LogChanges = $auditLog[1];

       $arg = array_filter($record);
       if (empty($arg)) {
         exit();
       }

       $criteria = "S_ROWID = '$ItemID' ";
       $action   = "UPDATE";
       $db->AutoExecute($ItemTable,$record,$action,$criteria);
       logAction($ItemID,$ItemTable,$user,$action,$LogChanges,$ModCode);
  }

function AddAlert($POST)
  {  
       global $db;
       $action   = "INSERT";
       $db->AutoExecute("dhalerts",$POST,$action);
  }


function CreateAcc($AccNo,$AccType,$user,$ModCode = null)
  {  
       global $db;
       $ModCode = is_null($ModCode) ? "" : $ModCode;
       $POST["AccountType"] = safehtml($AccType);
       $POST["AccountNo"]   = safehtml($AccNo);
       $POST["CreatedBy"]   = safehtml($user);
       $POST["DateCreated"]   = $db->GetOne("select current_timestamp");
       

       $action   = "INSERT";
       $tblName = "tbl_dalaaccounts";
       $db->AutoExecute($tblName,$POST,$action);
       $ItemID = $db->GetOne("select max(S_ROWID) from $tblName");
       logAction($ItemID,$tblName,$user,$action,$POST,$ModCode);
  }



function sendMail($MsgBody,$subject,$to = array(),$cc = array())
  {
    global $mail;
$fromName =  $this->GetConf("SendName","Mail");;
$mail->IsSMTP(); // enable SMTP
$mail->ClearAllRecipients();
$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = $this->GetConf("smtphost","Mail");
$mail->Port = $this->GetConf("smtpport","Mail"); // or 587
$mail->IsHTML(true);
$mail->Username = $this->GetConf("smtpuser","Mail");
$mail->Password = $this->GetConf("smtppass","Mail");
$mail->SetFrom($this->GetConf("smtpuser","Mail"),$fromName);
$mail->Subject = $subject;
$mail->Body = $MsgBody;
  
  $arg = array_filter($to);
   if (!empty($arg)) {
      foreach ($to as $key => $value) {
       $mail->AddAddress($value);
      }
  }

  $argcc = array_filter($cc);
  if (!empty($argcc)) {
      foreach ($cc as $key => $value) {
       $mail->AddCC($value);
      }
  }


//$mail->AddAttachment($rs->GetOne("ElementStorage","New_FileName","S_ROWID=10"));
 if(!$mail->Send()) {
    $ErrorMsg = "Mailer Error: " . $mail->ErrorInfo;
 } else {
    $ErrorMsg = "Message has been sent";
 }
$mail->ClearAllRecipients();

 return $ErrorMsg;
  }
// End Send Mail
  function getConf($confName,$confType)
{
  global $db;
  $confValue = $db->GetOne("select confValue from appconfigs where confType='$confType' and confName='$confName'");
  return $confValue;
}

}  // end class



/*CREATE USER 'quartoco_trinit'@'localhost' IDENTIFIED BY 'P@$$w0rd@3248';
GRANT ALL PRIVILEGES ON trinit.* TO 'quartoco_trinit'@'localhost';
FLUSH PRIVILEGES;*/

?>