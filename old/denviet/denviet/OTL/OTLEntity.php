<?php
namespace Kalephan\OTL;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Hash;

class OTLEntity extends EntityAbstract {
    public function __config() {
        return array(
            '#id' => 'id',
            '#name' => 'otl',
            '#class' => '\Kalephan\OTL\OTLEntity',
            '#title' => lks_lang('One time link'),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                ),
                'hash' => array(
                    '#name' => 'hash',
                ),
                'destination' => array(
                    '#name' => 'destination',
                ),
                'expired' => array(
                    '#name' => 'expired',
                ),
                'type' => array(
                    '#name' => '#type',
                ),
            ),
        );
    }

    public function setHash($destination, $type) {
        $onetimelink = new \stdClass();
        $onetimelink->destination = $destination;
        $onetimelink->expired = date('Y-m-d H:i:s', time() + config('lks.onetimelink expired', 172800)); // 2 days
        $onetimelink->hash = md5($onetimelink->destination . $onetimelink->expired . mt_rand());
        $onetimelink->type = $type;

        $this->saveEntity($onetimelink);

        return $onetimelink;
    }

    public function loadEntityWhere($attributes = []) {
        $entity = parent::loadEntityWhere($attributes);

        if ($entity) {
            $this->deleteEntity($entity->id);
        }

        return $entity;
    }

    public function loadEntityByHash($hash, $type, $attributes = []) {
        $attributes['where']['hash'] = $hash;
        $attributes['where']['type'] = $type;
        $attributes['where']['expired >='] = date('Y-m-d H:i:s');

        return $this->loadEntityWhere($attributes);
    }
}