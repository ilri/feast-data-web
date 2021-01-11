<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CropTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('crop_type');
        $this->setEntityClass('App\Model\Entity\CropType');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
    }
}
?>