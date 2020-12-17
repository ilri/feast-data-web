<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CommunityTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('community_type');
        $this->setEntityClass('App\Model\Entity\CommunityType');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('Site', ['foreignKey' => 'id_community_type']);
        $this->hasMany('FocusGroup', ['foreignKey' => 'id_community_type']);
        $this->hasMany('Respondent', ['foreignKey' => 'id_community_type']);
    }
}
?>