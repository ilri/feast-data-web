<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class AgricultureSystemTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('agriculture_system_type');
        $this->setEntityClass('App\Model\Entity\AgricultureSystemType');
        $this->hasMany('TechfitAssessmentTable', ['foreignKey' => 'id_agriculture_system_type']);
    }
}
?>