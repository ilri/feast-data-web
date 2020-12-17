<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class ProjectTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('project');
        $this->setEntityClass('App\Model\Entity\Project');
        $this->belongsTo('ProjectType', ['foreignKey' => 'id_project_type']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('SystemWorldRegion', ['foreignKey' => 'id_world_region']);
        $this->belongsTo('SystemCountry', ['foreignKey' => 'id_country']);
        $this->hasMany('Site', ['foreignKey' => 'id_project']);
    }
}
?>