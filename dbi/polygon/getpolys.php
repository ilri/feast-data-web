<?php 
$pid = filter_input(INPUT_GET, "pid");
$sql = "SELECT * FROM paths where pid=13";
   
$server     = "localhost";
$username   = "root";
$password   = 'P@$$w0rd';
$db         = "gm_polygons";  
$conn = mysqli_connect($server, $username, $password, $db);

$result = mysqli_query($conn, $sql);

$geojson = array('type' => 'FeatureCollection', 'features' => array());
   $cords = array();
while($row = mysqli_fetch_assoc($result)) {
   $cords[] = array(floatval($row['lng']),floatval($row['lat']));   
}
  array_push($cords, $cords[0]);
$marker = array(
       'type' => 'Feature',
            'properties' => array("name"=>"Null Island"),
            "geometry" => array(
                'type' => 'Polygon',
                'coordinates' => array($cords)
            )
    );

echo "<pre>";
array_push($geojson['features'], $marker);
//print_r($geojson);


 $str = '{"type":"FeatureCollection","features":[{"type":"Feature","properties":{},"geometry":{"type":"Polygon","coordinates":[[[12.3,41.51],[28.83,38.27],[41.84,32.55],[55.55,27.6],[71.37,29.54],[83.32,33.43],[94.92,36.6],[99.49,36.6],[111.45,35.17],[124.8,32.55],[130.78,35.17],[142.73,39.64],[152.58,43.58],[166.29,45.83],[163.83,56.17],[159.26,63.07],[154.69,68.66],[148.71,72.18],[140.63,75.93],[129.02,78.49],[114.26,80.3],[100.2,80.98],[87.54,81.2],[73.83,80.87],[59.41,79.62],[40.43,76.27],[28.13,71.07],[23.2,67.2],[20.04,63.55],[17.23,59.01],[15.12,54.16],[13.36,48.46],[12.3,41.51]]]}}]}';

  $list = json_decode($str,true);

echo json_encode($geojson); 
//echo "<br>";
//echo json_encode($list);
?>