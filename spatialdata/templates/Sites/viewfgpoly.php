     <?php
       $cords = array();
       $cords = $rst->loc_json != "" ? json_decode($rst->loc_json,true) : array();

  $venueName = $rst->focus_group_venue_name != "" ? $rst->focus_group_venue_name : $rst->site_name;
  $arg =array_filter($cords);
     if (!empty($arg)) {
       array_push($cords, $cords[0]);
       $center = array("lat"=>0,"lng"=>0);
     }
     else
     {
      $center = array("lat"=>floatval($rst->site_lat),"lng"=>floatval($rst->site_lng));
      $cords[] = array("lng"=>floatval($rst->site_lng),"lat"=>floatval($rst->site_lat));
     }
    

    ?>
  <script>
      var coordinates = <?php echo json_encode($cords); ?>;
      var heading = <?php echo json_encode($venueName); ?>;
      function initMap() {
        var container = document.getElementById("map");
        var map = new google.maps.Map(container, {
          zoom: 10,
          heading : heading ,
          disableDefaultUI: false,
          streetViewControl: false,
          center: { lat: 1.957709, lng: 37.2972044},
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
        var bounds = new google.maps.LatLngBounds();
        mypolyg.getPath().forEach(function (path, index) {
            bounds.extend(path);
        });
        
        map.fitBounds(bounds);

    /*    google.maps.event.addListener(map, 'bounds_changed', function() {
         
          var newCenter = map.getCenter();
           var newZoom = map.getZoom();
           //document.getElementById("newCenter").innerHTML= "Center :"+newCenter+" New Zoom :"+newZoom;
         //var ne = bounds.getNorthEast();
         //var sw = bounds.getSouthWest();
         
      });*/

      }
    </script>
   <style type="text/css">
       #map {
        height: 100%;
      }
   </style>

 <div class="content" >
    <h3>View Polygon for Focus Group :  <?= $rst->site_name;?> (<?= $rst->focus_group_venue_name; ?>) </h3>
      <hr>
   <div class="row">
       
    <aside class="column">
      <h3>Site Info</h3>
        <div class="side-nav">
          <?php 
            $CountFG = $rst->focus_group_count;
         $linkTitle = 'View Focus Groups';
           $contentString = "<p>Project Name : <b>".$rst->project_title."</b></p>";
          $contentString .= "<p>Site Name : <b>".$rst->site_name."</b></p>";
          $contentString .= "<p>Country: <b>".$rst->site_country."</b></p>";
          $contentString .= "<p>Venue Name: <b>".$rst->focus_group_venue_name."</b></p>";
          $contentString .= "<p>Community Group: <b>".$rst->focus_group_community."</b></p>";
          $contentString .= "<p>Community Type: <b>".$rst->focus_group_community_type."</b></p>";
          $contentString .= "<p>Sub Region: <b>".$rst->focu_group_sub_region."</b></p>";
      
     // $contentString .= "<p>Lat: <b><span id='lat'>".$rst->focus_group_lat."</span> </b> </p>";
      //$contentString .= "<p>Long:<b><span id='lon'>".$rst->focus_group_lng."</span></b></p>";
     
     echo $contentString;
          ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class=" view content" style="height:500px;">
            
            <div id="map" class="column-100"></div>  
           
         </div>
    </div>
</div>
 </div>