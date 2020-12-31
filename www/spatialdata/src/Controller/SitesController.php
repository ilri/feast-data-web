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
                $this->Flash->success(__('Cordinates Updated successfully'));
            }
            else
            {
                $this->Flash->error(__('Error Updating Cordinates'));
            }
            
            
            
        }
        
       $siteinfo = TableRegistry::get('export_project_site');
       $rst = $siteinfo->find('all', [ 'conditions' => ['site_id' => $siteid]])->first();
       $this->set(compact('rst'));
        
      
    }
    

    public function mngfgpoly($fgID)
    {
    
       $siteinfo = TableRegistry::get('export_focus_group');
       $rst = $siteinfo->find('all', [ 'conditions' => ['focus_group_id' => $fgID]])->first();
       $this->set('rst',$rst);
    }

     public function viewfgpoly($fgID = null)
    {
    
       $siteinfo = TableRegistry::get('export_focus_group');
       $rst = $siteinfo->find('all', [ 'conditions' => ['focus_group_id' => $fgID]])->first();
       $this->set('rst',$rst);
    }


    public function rmpolygon()
    {
      if( $this->request->is('ajax') ) {
     // echo $_POST['value_to_send'];
      $fgID = $this->request->getData("id_focus_group");
      $pathstr = array();
      $fgCordinates = TableRegistry::get('focus_group');
      $rstdata = $fgCordinates->find('all', [ 'conditions' => ['id' => $fgID]])->first();

      $rstdata->loc_json = null;
      $rstdata->geo_json = null;
      $fgCordinates->save($rstdata);
      /*$fgCordinates = TableRegistry::get('spatial_data_focus_group');
      $fgCordinates->deleteAll(['id_focus_group'=>$fgID]);*/
      echo "Success";
      die();
    }
    }
     

    public function updatepolygon()
    {
        if( $this->request->is('ajax') ) {
      $fgID = $this->request->getData("id_focus_group");
      $pathstr = $this->request->getData("paths");
      $fgCordinates = TableRegistry::get('focus_group');
      $rstdata = $fgCordinates->find('all', [ 'conditions' => ['id' => $fgID]])->first();

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
                "coordinates" => [$coordinates],
                "properties" => ["id" => $rstdata->id]
            ]
        ]);
      } else {
        $rstdata->geo_json = null;
      }
      $fgCordinates->save($rstdata);
      /* $fgCordinates = TableRegistry::get('spatial_data_focus_group');
      $fgCordinates->deleteAll(['id_focus_group'=>$fgID]);
        
         $list = json_decode($pathstr,true);
        foreach ($list as $key => $val) {
           $rstdata = $fgCordinates->newEntity($this->request->getData());
           $rstdata->id_focus_group = (int)$fgID;
           $rstdata->latitude = floatval($val["lat"]);
           $rstdata->longitude = floatval($val["lng"]);
           
           $fgCordinates->save($rstdata);
        }*/
        
     //echo $pathstr;
      die();
    }  
    }
}
