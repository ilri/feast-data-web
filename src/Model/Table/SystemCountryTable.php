<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class SystemCountryTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('country');
        $this->setEntityClass('App\Model\Entity\SystemCountry');
        $this->belongsTo('SystemWorldRegion', ['foreignKey' => 'id_world_region', 'propertyName' => 'world_region']);
        $this->hasMany('SystemCountryMajorRegion', ['foreignKey' => 'country_id']);
    }
}
?>