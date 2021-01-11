  <?php
  ini_set('memory_limit', '-1'); 
    $locations = array();
    $key = 0;
     foreach ($geosites as $val) {
		  $key += 1; 	
		$SiteID = $val->site_id;
        
        $CountFG = $val->focus_group_count;;
        $linkTitle = 'View Focus Groups';
        $linkSiteTitle = 'Manage Site Cordinates';
    $contentString = "<p>Project Name : <b>".$val->project_title."</b></p>";
    $contentString .= "<p>Site Name : <b>".$val->site_name."</b></p>";
    $contentString .= "<p>Major Region : <b>".$val->site_major_region."</b></p>";
    $contentString .= "<p>Country: <b>".$val->site_country_name."</b></p>";
    $contentString .= "<p>Community Type: <b>".$val->site_community_type."</b></p>";
    $contentString .= "<p>Venue Name: <b>".$val->venue_name."</b></p>";
    $link = $this->Html->link(__($linkTitle), ['action' => 'sitefg', $val->site_id]);
    $linksite = $this->Html->link(__($linkSiteTitle), ['action' => 'mngsitepoint', $val->site_id]);
    //$contentString .= "<p>$link | $linksite</p>";
    $locations[] = array($contentString,floatval($val->site_lat),floatval($val->site_lng),$key,$val->site_name." ($CountFG FG's)");
		   	
		   }

?>
  <script>
      // This example displays a marker at the center of sites.
      // When the user clicks the marker, an info window opens

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
   <div id='map'>  </div>
</div>