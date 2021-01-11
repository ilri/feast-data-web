<?php 
$pid = filter_input(INPUT_GET, "pid");
$sql = "SELECT * FROM paths where pid=$pid";
   
$server     = "localhost";
$username   = "root";
$password   = 'P@$$w0rd';
$db         = "gm_polygons";  
$conn = mysqli_connect($server, $username, $password, $db);

$result = mysqli_query($conn, $sql);

$geojson = array('type' => 'FeatureCollection', 'features' => array());
   $cords = array();
while($row = mysqli_fetch_assoc($result)) {
   $cords[] = array("lng"=>floatval($row['lng']),"lat"=>floatval($row['lat']));   
    $lng[] = floatval($row['lng']);
   $lat[] = floatval($row['lat']);
}
  array_push($cords, $cords[0]);

echo "<pre>";
//echo json_encode($cords);

print_r($lng);
print_r($lat);

$lat_min = min(array_values($lat));
$lat_max = max(array_values($lat));
$lng_min = min(array_values($lng));
$lng_max = max(array_values($lng));

  print_r($lat_min);
  echo "<br>";

$center = array("lat"=>floatval(($lat_max + $lat_min) / 2.0),"lng"=>floatval(($lng_max + $lng_min) / 2.0));

print_r($center);

?>