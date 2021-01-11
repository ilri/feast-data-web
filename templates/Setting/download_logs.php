<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    $content = file_get_contents ($logName);
    $fileName = basename($logName)."-".date('Ymdhms').".txt";
    $handle = fopen("tmp/feast/".$fileName, "w");
    fwrite($handle, $content);
    fclose($handle);

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$fileName);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($logName));
    readfile($logName);
    unlink("tmp/feast/".$fileName);
    exit;

?>