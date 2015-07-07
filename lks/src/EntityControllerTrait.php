<?php
namespace Kalephan\LKS;

use Kalephan\LKS\Facades\Output;

trait EntityControllerTrait {
    protected $entity;
    protected $pagetitle;

    public function getIndex()
    {
        if (!empty($this->pagetitle['index'])) {
            Output::titleAdd($this->pagetitle['index']);
        }

        $entities = [
            'entities' => [],
            'paginator' => '',
            'structure' => $this->entity->structure(),
        ];
        $entities = array_merge($entities, $this->entity->loadEntityPaginate());

        return view('page-entity-list', $entities);
    }

    public function getAdd()
    {
        # code...
    }
}