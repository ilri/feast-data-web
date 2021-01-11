<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class TechfitScaleTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('techfit_scale');
        $this->setEntityClass('App\Model\Entity\TechfitScale');
        $this->hasMany('CoreContextAttributeScore', ['foreignKey' => 'id_techfit_scale']);
    }
}
?>