<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CoreContextAttributeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('core_context_attribute');
        $this->setEntityClass('App\Model\Entity\CoreContextAttribute');
        $this->belongsTo('CoreContextAttributeType', ['foreignKey' => 'id_core_context_attribute_type']);
        $this->hasMany('CoreContextAttributeScore', ['foreignKey' => 'id_core_context_attribute']);
    }
}
?>