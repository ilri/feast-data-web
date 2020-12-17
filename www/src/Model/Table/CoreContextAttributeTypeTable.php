<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CoreContextAttributeTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('core_context_attribute_type');
        $this->setEntityClass('App\Model\Entity\CoreContextAttributeType');
        $this->hasMany('CoreContextAttribute', ['foreignKey' => 'id_core_context_attribute']);
    }
}
?>