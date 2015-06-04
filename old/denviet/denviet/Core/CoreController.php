<?php
namespace Kalephan\Core;

class CoreController {
    public function showHomepage($lks) {
        if ($lks->response->isAdminPanel()) {
            $lks->response->addContent(lks_render('homepage-admin'));
        }
        else {
            $lks->response->addContent(lks_render('homepage'));
        }
    }
}