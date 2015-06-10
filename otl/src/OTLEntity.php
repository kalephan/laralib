<?php
namespace Kalephan\OTL;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Hash;

class OTLEntity extends EntityAbstract
{
    public function __config()
    {
        return array(
            '#id' => 'token',
            '#name' => 'otl',
            '#class' => '\Kalephan\OTL\OTLEntity',
            '#title' => lks_lang('One time link'),
            '#fields' => array(
                'token' => array(
                    '#name' => 'token'
                ),
                'expired' => array(
                    '#name' => 'expired'
                ),
                'created_at' => array(
                    '#name' => 'created_at'
                ),
                'data' => array(
                    '#name' => 'data'
                )
            )
        );
    }

    public function setHash($data = [])
    {
        $now = time();
        
        $onetimelink = new \stdClass();
        $onetimelink->created_at = date('Y-m-d H:i:s', $now);
        $onetimelink->expired = date('Y-m-d H:i:s', $now + config('otl.expired', 172800)); // 2 days
        $onetimelink->token = md5($onetimelink->expired . mt_rand());
        $onetimelink->data = $data;
        
        $this->saveEntity($onetimelink);
        
        return $onetimelink;
    }
    
    public function saveEntity($entity_new, $active_action = false)
    {
        $entity_new->data = isset($entity_new->data) ? serialize($entity_new->data) : '';
        return parent::saveEntity($entity_new, $active_action);
    }
    
    public function loadEntity($entity_id, $check_active = false)
    {
        $entity = parent::loadEntity($entity_id, $check_active);
        $entity->data = isset($entity->data) ? unserialize($entity->data) : '';
        
        return $entity;
    }
}