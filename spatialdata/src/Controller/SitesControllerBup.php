<?php
declare(strict_types=1);

namespace App\Controller;
use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\Time;
class SitesController extends AppController
{   

  function geocode($address){
 
    // url encode the address
    $address = urlencode($address); 
    $googleMapKey = Configure::read('GOOGLE_MAP_KEY');
    // google map geocode api url
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$googleMapKey}";
 
    // get the json response
    $resp_json = file_get_contents($url);
     
    // decode the json
    $resp = json_decode($resp_json, true);
 
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
        $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
        $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            $data_arr["lati"] = $lati;
            $data_arr["longi"] = $longi;
            $data_arr["formatted_address"] = $formatted_address;

           
            return $data_arr;
             
        }else{
            return false;
        }
         
    }
 
    else{
        
        return false;
    }
}
   
    public function sitepoints()
    {
        $sites = TableRegistry::get('export_project_site');
        $query = $sites->find('all', [ 'conditions' => ['site_lat <>' => 0]]);
        $this->set('sites',$query);
    }


    public function index()
    {
        $sites = TableRegistry::get('export_project_site');
        $query = $sites->find()->order(['site_lat'=>'desc']);
        $sitelist = $this->paginate($query);
        $this->set(compact('sitelist'));
    }

    public function focusGroup($siteid)
    {

    }

    public function sitefg($siteid = null)
    {
       $fginfo = TableRegistry::get('export_focus_group');
       
        $query = $fginfo->find('all', [ 'conditions' => ['site_id' => $siteid]]);
        $fglist = $this->paginate($query);
        $this->set(compact('fglist'));
    }

    public function mngsitepoint ($siteid)
    {  
        if ($this->request->is('post')) {
           $postdata = $this->request->getData();
          
           $spTable = TableRegistry::get('spatial_data_site');
           $rstdata = $spTable->find('all', [ 'conditions' => ['id_site' => $siteid]])->first();
            
            if (empty($rstdata)) {
                $rstdata = $spTable->newEntity($this->request->getData());
            }

           
           $rstdata->id_site = (int)$this->request->getData("site_id");
           $rstdata->latitude = floatval($this->request->getData("site_lat"));
           $rstdata->longitude = floatval($this->request->getData("site_long"));
           
            if ($spTable->save($rstdata)) {
                $this->Flash->success(__('Coordinates Updated successfully'));
            }
            else
            {
                $this->Flash->error(__('Error Updating Coordinates'));
            }
            
            
            
        }
        
       $siteinfo = TableRegistry::get('export_project_site');
       $rst = $siteinfo->find('all', [ 'conditions' => ['site_id' => $siteid]])->first();
         if ($rst->site_lat == 0) {
          $venueName  = $rst->venue_name;
           $site_country_name = $rst->site_country_name != "" ? trim($venueName." ".$rst->site_country_name) : "East Africa";
           $defCoordinates = $this->geocode($site_country_name);
             if (!$defCoordinates) {
              $defCoordinates = $this->geocode($rst->site_country_name);
            }
           $rst->site_lat = $defCoordinates["lati"];
           $rst->site_lng = $defCoordinates["longi"];

           $spTable = TableRegistry::get('spatial_data_site');
           $rstdata = $spTable->find('all', [ 'conditions' => ['id_site' => $siteid]])->first();
            
            if (empty($rstdata)) {
                $rstdata = $spTable->newEmptyEntity();
            }

           $rstdata->id_site = (int)$rst->site_id;
           $rstdata->latitude = floatval($defCoordinates["lati"]);
           $rstdata->longitude = floatval($defCoordinates["longi"]);
           $spTable->save($rstdata);
         }
       $this->set(compact('rst'));
        
      
    }
    

    public function mngfgpoly($fgID)
    {
       $siteinfo = TableRegistry::get('export_focus_group');
       $rst = $siteinfo->find('all', [ 'conditions' => ['focus_group_id' => $fgID]])->first();

       if ($rst->site_lat == 0) {
          $venueName  = $rst->focus_group_venue_name;
           $site_country_name = $rst->site_country != "" ? trim($venueName." ".$rst->site_country) : "East Africa";
           $defCoordinates = $this->geocode($site_country_name);
            if (!$defCoordinates) {
              $defCoordinates = $this->geocode($rst->site_country);
            }
           $rst->site_lat = $defCoordinates["lati"];
           $rst->site_lng = $defCoordinates["longi"];
         }

       $this->set(compact('rst'));
    }

     public function viewfgpoly($fgID = null)
    {
    
       $siteinfo = TableRegistry::get('export_focus_group');
       $rst = $siteinfo->find('all', [ 'conditions' => ['focus_group_id' => $fgID]])->first();
         if ($rst->site_lat == 0) {
          $venueName  = $rst->focus_group_venue_name;
           $site_country_name = $rst->site_country != "" ? trim($venueName." ".$rst->site_country) : "East Africa";
           $defCoordinates = $this->geocode($site_country_name);
             if (!$defCoordinates) {
              $defCoordinates = $this->geocode($rst->site_country);
            }
           $rst->site_lat = $defCoordinates["lati"];
           $rst->site_lng = $defCoordinates["longi"];
         }
       $this->set('rst',$rst);
    }


    public function rmpolygon()
    {
      if( $this->request->is('ajax') ) {
     // echo $_POST['value_to_send'];
      $fgID = $this->request->getData("id_focus_group");
      $pathstr = array();
      $fgCoordinates = TableRegistry::get('focus_group');
      $rstdata = $fgCoordinates->find('all', [ 'conditions' => ['id' => $fgID]])->first();

      $rstdata->loc_json = null;
      $rstdata->geo_json = null;
      $fgCoordinates->save($rstdata);
      /*$fgCoordinates = TableRegistry::get('spatial_data_focus_group');
      $fgCoordinates->deleteAll(['id_focus_group'=>$fgID]);*/
      echo "Success";
      die();
    }
    }
     

    public function updatepolygon()
    {
        if( $this->request->is('ajax') ) {
      $fgID = $this->request->getData("id_focus_group");
      $pathstr = $this->request->getData("paths");
      $fgCoordinates = TableRegistry::get('spatial_data_focus_group');
      $rstdata = $fgCoordinates->find('all', [ 'conditions' => ['id_focus_group' => $fgID]])->first();
       if (empty($rstdata)) {
                $rstdata = $fgCoordinates->newEntity($this->request->getData());
                $rstdata->id_focus_group = (int)$this->request->getData("id_focus_group");
            }

      $rstdata->loc_json = $pathstr;
      if ($rstdata->loc_json) {
        $coordinates = [];
        $list = json_decode($rstdata->loc_json);
        foreach ($list as $val) {
            $coordinates[] = array_values(get_object_vars($val));
        }
        $rstdata->geo_json = json_encode([
            "type" => "Feature",
            "geometry" => [
                "type" => "Polygon",
                "coordinates" => [$coordinates]
            ],
            "properties" => ["id" => $fgID]
        ]);
      } 
      else
       {
        $rstdata->geo_json = null;
       }
      $fgCoordinates->save($rstdata);
     
      die();
    }  
    }
}
