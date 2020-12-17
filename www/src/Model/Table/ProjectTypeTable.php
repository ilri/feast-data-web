<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class ProjectTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('project_type');
        $this->setEntityClass('App\Model\Entity\ProjectType');
        $this->hasMany('Project', ['foreignKey' => 'id_project_type']);
    }
}
?>