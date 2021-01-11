<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class SystemWorldRegionTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('world_region');
        $this->setEntityClass('App\Model\Entity\SystemWorldRegion');
        $this->hasMany('SystemCountry', ['foreignKey' => 'id_world_region']);
    }
}
?>