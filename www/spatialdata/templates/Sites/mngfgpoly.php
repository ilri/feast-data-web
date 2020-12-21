     <?php
       $cords = array();
       $cords = $rst->geo_json != "" ? json_decode($rst->geo_json,true) : array();

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
      $(document).ready(function () {

     $("#btnDelete").click(function(){
      var ajaxdata = $("#ajaxForm").serializeArray();
        if (confirm("Are you sure you want remove this Polygon?")) {
           $.ajax({
        type:"POST",
        data: ajaxdata, 
        url:"/spatialdata/sites/rmpolygon/",
        success : function(data) {
           location.reload();
        },
        error : function() {
           alert(data);
        }
    });
        }
   
     }); // End BtnDelete


     
 });

      var coordinates = <?php echo json_encode($cords); ?>;
      var heading = <?php echo json_encode($venueName); ?>;
      var center = <?php echo json_encode($center); ?>;

            let map;
            let bttn;
            let path;
            let params;
       

      function initMap() {
        var container = document.getElementById("map");
         bttn =  document.getElementById('btnUpdate');
        fgid = document.getElementById("id_focus_group");
        var map = new google.maps.Map(container, {
          zoom: 10,
          //heading : heading ,
          center: center,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          disableDefaultUI: false,
          mapTypeControl: true,
          mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
            mapTypeIds: [ 'roadmap', 'terrain', 'satellite', 'hybrid' ]
          }
        });

        var mypolyg = new google.maps.Polygon({
          path: coordinates,
          geodesic: true,
          strokeColor: '#FF0000',
          strokeOpacity: 0.8,
          strokeWeight: 3,
          fillColor: '#FF0000',
          fillOpacity: 0.35,
          draggable:true,
          editable:true
        });

          google.maps.event.addListener( map, 'click', e=>{
                    path= mypolyg.getPath();
                    path.push( e.latLng );
                    
            });

        mypolyg.setMap(map);
        var bounds = new google.maps.LatLngBounds();
        mypolyg.getPath().forEach(function (path, index) {
            bounds.extend(path);
        });
        
        map.fitBounds(bounds);



         bttn.addEventListener('click',e=>{
                        path = mypolyg.getPath();
                        polypath=[];

                        for( let i=0; i < path.length; i++ ){
                            let point=path.getAt( i );
                            polypath.push( { lat:point.lat(), lng:point.lng() } )
                        }
                       
                            
                        
        var ajaxdata = $("#ajaxForm").serializeArray();
        ajaxdata.push({name: 'paths', value: JSON.stringify(polypath)});
          
        if (confirm("Are you sure you want update this Polygon?")) {
           $.ajax({
        type:"POST",
        data: ajaxdata, 
        url:"/spatialdata/sites/updatepolygon/",
        success : function(data) {
          location.reload();
          //alert(data);
        },
        error : function() {
           alert(data);
        }
           });
        }
          
                });

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
    <h3>Manage Polygon for Focus Group :  <?= $rst->site_name;?> (<?= $rst->focus_group_venue_name; ?>) </h3>
      <hr>
   <div class="row">
       <h3></h3>
    <aside class="column">
      
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
      
      //$contentString .= "<p>Lat: <b><span id='lat'>".$rst->focus_group_lat."</span> </b> </p>";
     // $contentString .= "<p>Long:<b><span id='lon'>".$rst->focus_group_lng."</span></b></p>";
     
     echo $contentString;
          ?>
        </div>
    </aside>
   
    <div class="column-responsive column-80">
      <span id="newCenter"></span>
      <div class="message default text-center">
               <div class="btn-group" role="group" aria-label="...">
  <button type="button" id="btnUpdate" class="btn btn-success btn-sm" title="Edit Layers"><i class="fa fa-edit"></i> Update Path</button>
  <button type="button" id="btnDelete" class="btn btn-danger btn-sm" title="Remove Layers"><i class="fa fa-trash"></i> Remove Polygon</button>
</div>
      </div>
        <div class=" view content" style="height:500px;">
            
            <div id="map" class="column-100"></div>  
           
         </div>
    </div>
</div>
 </div>


      <?php
      echo $this->Form->create(null,['id' => "ajaxForm"]);
      // Hard code the user for now.
      echo $this->Form->control('id_focus_group', ['type' => 'hidden', 'value' => $rst->focus_group_id]);
      echo $this->Form->end();
  ?>


 