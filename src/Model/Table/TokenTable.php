<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class TokenTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('user_token');
        $this->setEntityClass('App\Model\Entity\Token');
        $this->belongsTo('User', ['className' => 'User', 'foreignKey' => 'user_id', 'propertyName' => 'user']);
        $this->addBehavior('Timestamp');
    }
}
?>