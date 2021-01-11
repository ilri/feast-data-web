<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);
   include("con_db.php");
  global $db;
  //$db->debug=1;
  
   //$getSites = $db->GetArray("SELECT * FROM export_project_site where site_lat <> 0");
  $getSites = $db->GetArray("SELECT * FROM export_project_site");
   $locations = array();
   foreach ($getSites as $key => $getSite) {
    $SiteID = $getSite["site_id"];
    $CountFG = $db->GetOne("select count(*) from focus_group where id_site=$SiteID");
        $latlng = array("lat"=>floatval($getSite["site_lat"]),"lng"=>floatval($getSite["site_lng"]));
    $contentString = "<p>Project Name : <b>".$getSite["project_title"]."</b></p>";
    $contentString .= "<p>Site Name : <b>".$getSite["site_name"]."</b></p>";
    $contentString .= "<p>Major Region : <b>".$getSite["site_major_region"]."</b></p>";
    $contentString .= "<p>Country: <b>".$getSite["site_country_name"]."</b></p>";
    $contentString .= "<p>Community Type: <b>".$getSite["site_community_type"]."</b></p>";
    $contentString .= "<p>Venue Name: <b>".$getSite["venue_name"]."</b></p>";
    $contentString .= "<p><a href='' title='Click to view Focus Groups'>View Focus Groups ($CountFG FG's)</a></p>";
    $locations[] = array($contentString,floatval($getSite["site_lat"]),floatval($getSite["site_lng"]),$key+1,$getSite["site_name"]." ($CountFG FG's)");
   }
   
?>

 
 <!DOCTYPE html>
<html>
  <head>
     <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
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
        var locations = <?php echo json_encode( $locations);?>

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 3,
      center: new google.maps.LatLng(-8.783195, 34.508523),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map,
        title: locations[i][4],
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);

          infowindow.open(map, marker);
        }
      })(marker, i));
    }
      }
    </script>
  </head>
  <body>
    <div id="map"></div>
  </body>
</html>