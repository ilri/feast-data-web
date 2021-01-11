<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class ProjectViewTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('vw_project');
        $this->setPrimaryKey('id');
        $this->setEntityClass('App\Model\Entity\ProjectView');
        $this->belongsTo('ProjectType', ['foreignKey' => 'id_project_type']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('SystemWorldRegion', ['foreignKey' => 'id_world_region']);
        $this->belongsTo('SystemCountry', ['foreignKey' => 'id_country']);
        $this->hasMany('SiteView', ['foreignKey' => 'id_project']);
    }
}
?>