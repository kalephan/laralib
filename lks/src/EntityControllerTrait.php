<?php
namespace Kalephan\LKS;

use Kalephan\LKS\Facades\Form;
use Kalephan\LKS\Facades\Output;

trait EntityControllerTrait {

    protected $entity;
    protected $pagetitle;

    public function getIndex($id = null)
    {
        // List
        if (empty($id)) {
            return $this->getList();
        }

        // Read
        return $this->getRead($id);
    }

    public function getCreate()
    {
        if (!empty($this->pagetitle['create'])) {
            Output::titleAdd($this->pagetitle['create']);
        }

        return lks_view('page', [
            'content' => Form::build([$this->entity, 'formCreate'])
        ]);
    }

    public function postCreate()
    {
        return Form::submit();
    }

    public function getRead($id)
    {
        return 'getRead';
    }

    public function getUpdate($id)
    {
        return 'getUpdate';
    }

    public function postUpdate($id)
    {
        return Form::submit();
    }

    public function getDelete($id)
    {
        return 'getDelete';
    }

    public function postDelete($id)
    {
        return Form::submit();
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