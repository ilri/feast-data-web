<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class InterventionTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('intervention');
        $this->setEntityClass('App\Model\Entity\Intervention');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
    }
}

?>