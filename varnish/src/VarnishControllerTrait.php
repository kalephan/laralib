<?php

namespace Kalephan\Varnish;

trait VarnishControllerTrait
{

    public function getIndex()
    {
        $content = new \stdClass();
        $content->varnish = [];
        event('varnish.load', $content);
        
        return lks_view('page-varnish', ['content' => json_encode(array_filter($content->varnish))]);
    }
}