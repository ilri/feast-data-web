<?php
   error_reporting(E_ALL);
  ini_set('display_errors', 0);
  echo "<pre>";
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');
  


  $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    $targetPath  = "uploads/biodata.xlsx";
    $Reader = new SpreadsheetReader($targetPath);
          
        $sheetCount = count($Reader->sheets());
        print_r($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++)
        {
            
            $Reader->ChangeSheet($i);
            print_r($Reader);
            foreach ($Reader as $Row)
            {
                print_r($Row);
             }
        
         }
  
 
  
?>

