<?php
namespace Kalephan\LKS;

use Kalephan\LKS\Facades\Output;

trait EntityControllerTrait {
    protected $entity;
    protected $pagetitle;

    public function getIndex()
    {
        Output::titleAdd($this->pagetitle['index']);

        $data = [];
        if ($entities = $this->entity->loadEntityPaginate()) {
            $data['table'] = lks_entities2table($entities['entities'], $this->entity->structure());
            $data['paginator'] = $entities['paginator']->render();
        }

        return view('page-entity-list', $data);
    }
}