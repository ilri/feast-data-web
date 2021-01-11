<?php
  ini_set('memory_limit', '-1'); 
    $locations = array();
    $key = 0;
     foreach ($sites as $val) {
		  $key += 1; 	
		$SiteID = $val->site_id;
        //$CountFG = $db->GetOne("select count(*) from focus_group where id_site=$SiteID");
        $CountFG = 0;
        
    $contentString = "<p>Project Name : <b>".$val->project_title."</b></p>";
    $contentString .= "<p>Site Name : <b>".$val->site_name."</b></p>";
    $contentString .= "<p>Major Region : <b>".$val->site_major_region."</b></p>";
    $contentString .= "<p>Country: <b>".$val->site_country_name."</b></p>";
    $contentString .= "<p>Community Type: <b>".$val->site_community_type."</b></p>";
    $contentString .= "<p>Venue Name: <b>".$val->venue_name."</b></p>";
    $contentString .= "<p><a href='' title='Click to view Focus Groups'>View Focus Groups ($CountFG FG's)</a></p>";
    $locations[] = array($contentString,floatval($val->site_lat),floatval($val->site_lng),$key,$val->site_name." ($CountFG FG's)");
		   	
		   }

?>
   <style type="text/css">
   	 #map {
        height: 100%;
      }
   </style>
 <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVZxvbF6TGfzB_zSuaXRo8rdPW-gGb9kc&callback=initMap&libraries=&v=weekly"
      defer
    ></script>
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
    

  <div class="row">
   
    <div class="column-responsive column-100" style="height:400px;">
        <div class="maps view content" >
            <h3><?php echo $key." Site Displayed"?></h3>
            <div id='map'></div>
        </div>
    </div>

</div>
    