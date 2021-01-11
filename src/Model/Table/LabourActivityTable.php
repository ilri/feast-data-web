<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class LabourActivityTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('labour_activity');
        $this->setEntityClass('App\Model\Entity\LabourActivity');
        $this->belongsTo('FocusGroup', ['foreignKey' => 'id_focus_group']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
    }
}

?>