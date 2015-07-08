<?php
namespace Kalephan\LKS;

use Kalephan\LKS\Facades\Output;

trait EntityControllerTrait {

    protected $entity;
    protected $pagetitle;

    public function getCreate()
    {
        return 'getCreate';
    }

    public function postCreate()
    {
        return 'postCreate';
    }

    public function getIndex($id)
    {
        return 'getIndex';
    }

    public function getUpdate($id)
    {
        return 'getUpdate';
    }

    public function postUpdate($id)
    {
        return 'postUpdate';
    }

    public function getDelete($id)
    {
        return 'getDelete';
    }

    public function postDelete($id)
    {
        return 'postDelete';
    }

    public function getList()
    {
        if (!empty($this->pagetitle['list'])) {
            Output::titleAdd($this->pagetitle['list']);
        }

        $entities = [
            'entities' => [],
            'paginator' => '',
            'structure' => $this->entity->structure(),
        ];
        $entities = array_merge($entities, $this->entity->loadEntityPaginate());

        return view('page-entity-list', $entities);
    }
}