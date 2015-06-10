<?php
namespace Kalephan\LKS\Approve;

use Kalephan\LKS\EntityAbstract;

class ApproveEntity extends EntityAbstract
{

    public function __config()
    {
        return array(
            '#name' => 'entity_approve',
            '#class' => '\Kalephan\LKS\Approve\ApproveEntity',
            '#title' => lks_lang('Entity Approve'),
            '#id' => 'key',
            '#fields' => array(
                'key' => array(
                    '#name' => 'key',
                    /*'#title' => lks_lang('Khóa'),
                    '#type' => 'text',*/
                ),
                'value' => array(
                    '#name' => 'value',
                    /*'#title' => lks_lang('Giá trị'),
                    '#type' => 'textarea',*/
                )
            )
        );
    }

    public function saveEntity($entity_new)
    {
        if (isset($entity_new->value)) {
            $entity_new->value = serialize($entity_new->value);
        }
        
        return parent::saveEntity($entity_new);
    }

    public function loadEntityWhere($entity_id, $attributes = [])
    {
        $entity = parent::loadEntityWhere($entity_id, $attributes);
        
        if (! empty($entity->value)) {
            $entity->value = unserialize($entity->value);
        }
        
        return $entity;
    }

    public function approve($entity_class, $entity_id)
    {
        $lks = & lks_instance_get();
        
        $entity_obj = $lks->load($entity_class);
        $entity = $entity_obj->loadEntity($entity_id);
        
        if (! empty($entity->approve) && $entity_approve = $this->loadEntity($entity->approve)) {
            $entity = $entity_approve;
            $entity_obj->saveEntity($entity, true);
            
            $title = $this->structure->table . ' #' . (! empty($entity->title) ? $entity->title : $entity->{$this->structure->id});
            $lks->response->addMessage(lks_lang('"%entity_title" đã được phê duyệt thành công', [
                '%entity_title' => $title
            ]));
        }
    }
}