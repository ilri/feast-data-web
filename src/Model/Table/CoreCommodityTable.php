<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CoreCommodityTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('core_commodity');
        $this->setEntityClass('App\Model\Entity\CoreCommodity');
        $this->hasMany('TechfitAssessmentTable', ['foreignKey' => 'id_core_commodity']);
    }
}
?>