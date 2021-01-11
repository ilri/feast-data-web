
<?php 
$pid = filter_input(INPUT_GET, "pid");
$sql = "SELECT * FROM paths where pid=$pid";
   
$server     = "localhost";
$username   = "root";
//$password   = 'P@$$w0rd';
//$db         =   'gm_polygons';
$password   = 'Feast19@254';
$db         =   'spatial_test';
$conn = mysqli_connect($server, $username, $password, $db);

$result = mysqli_query($conn, $sql);

   $cords = array();
   $lng= array();
   $lat = array();
while($row = mysqli_fetch_assoc($result)) {
   $cords[] = array("lng"=>floatval($row['lng']),"lat"=>floatval($row['lat']));
   $lng[] = floatval($row['lng']);
   $lat[] = floatval($row['lat']);
}
  array_push($cords, $cords[0]);
  $lat_min = min(array_values($lat));
$lat_max = max(array_values($lat));
$lng_min = min(array_values($lng));
$lng_max = max(array_values($lng));
  
  $center_lat = floatval(($lat_max + $lat_min) / 2.0);
$center_lng = floatval(($lng_max + $lng_min) / 2.0);
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Simple Polylines</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVZxvbF6TGfzB_zSuaXRo8rdPW-gGb9kc&callback=initMap&libraries"
      defer
    ></script>
    <style type="text/css">
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        width: 90%;
                min-height: 90vh;
                height:auto;
                box-sizing:border-box;
                margin: auto;
                float:none;
                margin:1rem auto;
                background:whitesmoke;
                padding:1rem;
                border:1px solid gray;
                display:block;
      }

      /* Optional: Makes the sample page fill the window. */
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
    <script>
      // This example creates a 2-pixel-wide red polyline showing the path of
      // the first trans-Pacific flight between Oakland, CA, and Brisbane,
      // Australia which was made by Charles Kingsford Smith.

       var coordinates = <?php echo json_encode($cords); ?>;
      
      

     
      function initMap() {
        var container = document.getElementById("map");
        var map = new google.maps.Map(container, {
          zoom: 15,
          heading : 'Kenya' ,
          center: { lat: <?php echo $center_lat;?>, lng: <?php echo $center_lng;?> },
          mapTypeId: "terrain",
        });

        var mypolyg = new google.maps.Polygon({
          path: coordinates,
          geodesic: true,
          strokeColor: "#FF0000",
          strokeOpacity: 1.0,
          strokeWeight: 2,
        });
        mypolyg.setMap(map);

           // Create the bounds object
    var bounds = new google.maps.LatLngBounds();

    // Get paths from polygon and set event listeners for each path separately
    mypolyg.getPath().forEach(function (path, index) {
        bounds.extend(path);
    });
    
    map.fitBounds(bounds);

        google.maps.event.addListener(map, 'bounds_changed', function() {
         
          var newCenter = map.getCenter();
           var newZoom = map.getZoom();
           //document.getElementById("newCenter").innerHTML= "Center :"+newCenter+" New Zoom :"+newZoom;
         //var ne = bounds.getNorthEast();
         //var sw = bounds.getSouthWest();
         
      });

      }
    </script>
  </head>
  <body>
   
    <div id="map"></div>
    
  </body>
</html>