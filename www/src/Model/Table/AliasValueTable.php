<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\Session;

class AliasValueTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('alias_values');
        $this->setEntityClass('App\Model\Entity\AliasValue');
        $this->belongsTo('Project', ['foreignKey' => 'table_id', 'conditions' => ['table_name' => 'project']]);
        $this->belongsTo('Site', ['foreignKey' => 'table_id', 'conditions' => ['table_name' => 'site']]);
        $this->belongsTo('FocusGroup', ['foreignKey' => 'table_id', 'conditions' => ['table_name' => 'focus_group']]);
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always'
                ]
            ]
        ]);
    }

    public function beforeSave($event, $entity, $options)
    {
        $userId = (new Session())->read('Auth.User.id');
        if ($entity->isNew()) {
            $entity->created_by = $userId;
        }
        $entity->updated_by = $userId;
    }
}
?>