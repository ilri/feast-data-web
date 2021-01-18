<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class UserTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('user');
        $this->setEntityClass('App\Model\Entity\User');
        $this->belongsTo('UserGender', ['className' => 'UserGender', 'foreignKey' => 'gender_id', 'propertyName' => 'gender']);
        $this->belongsTo('SystemApprovalStatus', ['className' => 'SystemApprovalStatus', 'foreignKey' => 'user_approval_status_id', 'propertyName' => 'user_approval_status']);
        $this->belongsTo('SystemCountry', ['className' => 'SystemCountry', 'foreignKey' => 'contact_country_id', 'propertyName' => 'country']);
        $this->belongsTo('SystemCountryMajorRegion', ['className' => 'SystemCountryMajorRegion', 'foreignKey' => 'contact_country_major_region_id', 'propertyName' => 'country_major_region']);
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always',
                ],
            ]
        ]);
    }
}
?>
