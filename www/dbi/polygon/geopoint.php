<?php 
$pid = filter_input(INPUT_GET, "pid");
error_reporting(E_ALL);
ini_set('display_errors', 1);
   include("con_db.php");
  global $db;
  //$db->debug=1;
  $SiteID = $pid;
   $getSite = $rs->row("export_project_site","site_id=$SiteID");
   $address = $getSite["site_name"];
   //$georst = $rs->geocode($address);
   
   $latlng = array("lat"=>floatval($getSite["site_lat"]),"lng"=>floatval($getSite["site_lng"]));
    $contentString = "<p>Project Name : <b>".$getSite["project_title"]."</b></p>";
    $contentString .= "<p>Site Name : <b>".$getSite["site_name"]."</b></p>";
    $contentString .= "<p>Major Region : <b>".$getSite["site_major_region"]."</b></p>";
    $contentString .= "<p>Country: <b>".$getSite["site_country_name"]."</b></p>";
    $contentString .= "<p>Community Type: <b>".$getSite["site_community_type"]."</b></p>";
    $contentString .= "<p>Venue Name: <b>".$getSite["venue_name"]."</b></p>";
    
    //echo "<pre>";
    print_r($latlng);
    //exit();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Info Windows</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVZxvbF6TGfzB_zSuaXRo8rdPW-gGb9kc&callback=initMap&libraries=&v=weekly"
      defer
    ></script>
    <style type="text/css">
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
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
      // This example displays a marker at the center of Australia.
      // When the user clicks the marker, an info window opens.
      function initMap() {
        var uluru = <?php echo json_encode($latlng);?>;
        var uluru2 = { lat: 8.3747765, lng: 4.1513764};
        var title = <?php echo json_encode($getSite['site_name']);?>;
        var map = new google.maps.Map(document.getElementById("map"), {
          zoom: 10,
          center: uluru,
        });
        var contentString = <?php echo json_encode($contentString);?>;;
        const infowindow = new google.maps.InfoWindow({
          content: contentString,
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map,
          title: title,
        });

        

          marker.setMap(map);
          /*bounds  = new google.map.LatLngBounds();
          loc = new google.map.LatLng(marker.position.lat(), marker.position.lng());
          bounds.extend(loc);
          map.fitBounds(bounds); */      
          //map.panToBounds(bounds);     

        marker.addListener("click", () => {
          infowindow.open(map, marker);
        });
      }
    </script>
  </head>
  <body>
    <div id="map"></div>
  </body>
</html>