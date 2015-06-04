<?php
namespace Kalephan\Event;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Cache;

class EventEntity extends EntityAbstract {
    private $event = [];

    public function __config() {
        return array(
            '#id' => 'id',
            '#name' => 'events',
            '#class' => '\Kalephan\Event\EventEntity',
            '#title' => lks_lang('Event'),
            '#settings' => array(
                'structure_alter' => false,
            ),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                ),
                'name' => array(
                    '#name' => 'name',
                ),
                'class' => array(
                    '#name' => 'class',
                ),
                'weight' => array(
                    '#name' => 'weight',
                ),
            ),
        );
    }

    public function loadEntityAllByEvent($event) {
        $cache_name = __METHOD__ . "-$event";
        $cache = Cache::get($cache_name);
        if ($cache !== NULL) {
            return $cache;
        }

        $attr = array(
            'where' => ['name' => $event],
            'select' => '*',
        );
        $entities = parent::loadEntityAll($attr);

        Cache::forever($cache_name, $entities);
        return $entities;
    }

    public function getEvent($event) {
        if (array_key_exists($event, $this->event)) {
            return $this->event[$event];
        }

        return [];
    }

    public function fire($event, $args) {
        $responses = [];

        // Load event from DB
        $events = $this->loadEntityAllByEvent($event);

        // Load run-time event
        $events = array_merge($events, $this->getEvent($event));

        if(count($events)) {
            foreach ($events as $value) {
                $segments = explode('@', $value->class);
                $method = !empty($segments[1]) ? $segments[1] : 'handle';

                if (!empty($segments[2])) {
                    $args[] = $segments[2];
                }

                $response = call_user_func_array([lks_instance_get()->load($segments[0]), $method], $args);

                if ($response === false) break;
                $responses[] = $response;
            }
        }

        return $responses;
    }

    public function listen($event, $class, $weight = 0) {
        $listen = new \stdClass;
        $listen->name = $event;
        $listen->class = $class;
        $listen->weight = $weight;

        $this->event[$event][] = $listen;
    }
}