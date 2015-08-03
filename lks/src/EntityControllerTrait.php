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

        return lks_view($this->_getView('page'), [
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
        $entity = $this->entity->loadEntity($id, false, false);
        if (!$entity) {
            abort(404);
        }

        if (!empty($this->pagetitle['update'])) {
            Output::titleAdd($this->pagetitle['update']);
        }

        return lks_view($this->_getView('page'), [
            'content' => Form::build([$this->entity, 'formUpdate'], $entity)
        ]);
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

        return view($this->_getView('page-entity-list'), $entities);
    }

    protected function _getView($page)
    {
        $pages = [
            $page . '-' . str_slug(str_replace('\\', '-', $this->entity->structure()->class)),
            $page,
        ];

        foreach ($pages as $page) {
            if (view()->exists($page))
            {
                return $page;
            }
        }

        return;
    }
}