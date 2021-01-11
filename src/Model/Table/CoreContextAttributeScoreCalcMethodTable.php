<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CoreContextAttributeScoreCalcMethodTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('core_context_attribute_score_calc_method');
        $this->setEntityClass('App\Model\Entity\CoreContextAttributeScoreCalcMethod');
        $this->hasMany('CoreContextAttributeScore', ['foreignKey' => 'id_core_context_attribute_score_calc_method']);
    }
}
?>