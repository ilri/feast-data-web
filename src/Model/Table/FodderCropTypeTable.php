<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class FodderCropTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('fodder_crop_type');
        $this->setEntityClass('App\Model\Entity\FodderCropType');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('FodderCropCultivation', ['foreignKey' => 'id_fodder_crop_type']);
    }
}
?>