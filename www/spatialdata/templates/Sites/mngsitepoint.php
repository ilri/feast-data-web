 <?php
   $latlng = array("lat"=>floatval($rst->site_lat),"lng"=>floatval($rst->site_lng));
   $str = "fed";
 ?>
  <script>
      // This example displays a marker at the center of Australia.
      // When the user clicks the marker, an info window opens.
      function initMap() {
        var centerlatlng = <?php echo json_encode($latlng);?>;
       
        var title = <?php echo json_encode($rst->site_name);?>;
        var map = new google.maps.Map(document.getElementById("map"), {
          zoom: 10,
          center: centerlatlng,
        });
        var contentString = title;
        const infowindow = new google.maps.InfoWindow({
          content: contentString,
        });
        var marker = new google.maps.Marker({
          position: centerlatlng,
           map: map,
          draggable:true,
          //position: results[0].geometry.location
          title: title,
        });

 
          marker.setMap(map);
          
          google.maps.event.addListener(marker, 'dragend', function(marker){
        var latLng = marker.latLng; 
        currentLatitude = latLng.lat();
        currentLongitude = latLng.lng();
        $("#site-lat").val(currentLatitude);
        $("#site-long").val(currentLongitude);
        $("#lat").html(currentLatitude);
        $("#lon").html(currentLongitude);
     });    

        marker.addListener("click", () => {
          infowindow.open(map, marker);
        });
      }
    </script>
   <style type="text/css">
   	   #map {
        height: 100%;
      }
   </style>

 <div class="content" >
      <h3>Manage Site Point for :  <?= $rst->site_name;?> </h3>
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
   			 $contentString .= "<p>Major Region : <b>".$rst->site_major_region."</b></p>";
   		 $contentString .= "<p>Country: <b>".$rst->site_country_name."</b></p>";
    	$contentString .= "<p>Community Type: <b>".$rst->site_community_type."</b></p>";
    	$contentString .= "<p>Venue Name: <b>".$rst->venue_name."</b></p>";
    	$contentString .= "<p>Lat: <b><span id='lat'>".$rst->site_lat."</span> </b> Long:<b><span id='lon'>".$rst->site_lng."</span></b></p>";
   	 $link = $this->Html->link(__($linkTitle), ['action' => 'sitefg', $rst->site_id]);
   	 $contentString .= "<p>$link</p>";
     echo $contentString;
        	?>
            
        </div>
    </aside>
    <div class="column-responsive column-80">
      <div class="message default text-center">
          <small>NB: Please remember to click the Update Cordinates button after changing the location of the Site point</small>  
          <a href="#" data-toggle="modal" data-target="#sitepoint-modal" class="btn btn-sm btn-success pull-right">Update Cordinates</a>
      </div>
        <div class=" view content" style="height:500px;">
        	  
            <div id="map" class="column-100"></div>  
           
         </div>
    </div>
</div>
 </div>


 <div class='modal fade' id="sitepoint-modal" tabindex='-1' role='dialog' aria-labeled-by='setting_modal_label' aria-hidden='true'>
    <div class="modal-dialog">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Update Cordinates</h2>
            </div>
            <div class="modal-body">
               <?php
                echo $this->Form->create(null);
                // Hard code the user for now.
                echo $this->Form->control('site_id', ['type' => 'hidden', 'value' => $rst->site_id]);
                echo $this->Form->control('site_lat',['label'=>'Site Latitude','value' => floatval($rst->site_lat)]);
                echo $this->Form->control('site_long',['label'=>'Site Longtitude','value' => floatval($rst->site_lng)]);
              
                echo $this->Form->button(__('Update Cordinates'),['class'=>'btn btn-success']);
                echo $this->Form->end();
            ?>
            </div><!-- end ModalBody -->

        </div><!-- End Modal-content -->
      </div><!-- end Modal dialog -->
</div> <!-- end Modal -->