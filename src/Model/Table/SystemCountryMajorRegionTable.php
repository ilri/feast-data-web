<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class SystemCountryMajorRegionTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('system_country_major_region');
        $this->setEntityClass('App\Model\Entity\SystemCountryMajorRegion');
        $this->belongsTo('SystemCountry', ['foreignKey' => 'country_id', 'propertyName' => 'country']);
    }
}
?>