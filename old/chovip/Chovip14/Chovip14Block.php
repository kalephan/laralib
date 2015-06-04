<?php
namespace Chovip\Chovip14;

use Illuminate\Support\Facades\Cookie;

class Chovip14Block {
    public function headerHome($block) {
        $lks =& lks_instance_get();

        $data = array(
            'header_top' => '',
            'header' => '',
            'primary_menu' => '',
        );
        $header_index = true;

        // User panel
        if ($lks->response->isUserpanel()) {
            $data['header_top'] = lks_render('block-header-top-ucp');
            $header_index = false;
        }

        // Cart
        if (0==1) {
            $header_index = false;
        }

        // Ec
        if (0==1) {
            $header_index = false;
        }

        if ($header_index) {
            $data['header_top'] = lks_render('block-header-top-index');
            $data['header'] = lks_render('block-header-header-index');
            $data['primary_menu'] = lks_render('block-header-menu-index');
        }

        return lks_render('block-header', $data);
    }

    public function headerHomeAccess(&$block) {
        $lks =& lks_instance_get();
        $body_class = 'header_index';

        // User panel
        if ($lks->response->isUserpanel()) {
            $body_class = 'manager';
        }

        // Cart
        if (0==1) {
            $body_class = 'header_cart';
        }

        // Ec
        if (0==1) {
            $body_class = 'shop';
        }

        lks_event_listen('body_class.buildData', "\Kalephan\BodyClass\BodyClassEvent@addClass@$body_class");

        return true;
    }

    public function contentTopHome($block) {
        return lks_render('block-home-content-top');
    }

    public function contentTopHomeAccess(&$block) {
        return lks_instance_get()->response->isFrontPage() && !lks_instance_get()->response->isUserpanel();
    }

    public function contentBottomInfo($block) {
        return lks_render('block-content-bottom-info');
    }

    public function contentBottomInfoAccess(&$block) {
        $support_url = array(
            '/',
        );

        return in_array(lks_url_current_with_prefix(), $support_url);
    }

    public function contentBottomProduct($block) {
        return lks_render('block-content-bottom-product');
    }

    public function contentBottomProductAccess(&$block) {
        $unsupport_prefix = ['up'];
        $unsupport_segment_0 = ['article', 'ec-topic', 'user'];

        $lks = lks_instance_get();

        return !in_array($lks->request->prefix(), $unsupport_prefix)
            && !in_array($lks->request->segment(0), $unsupport_segment_0);
    }

    public function contentBottomMenu($block) {
        return lks_render('block-content-bottom-menu');
    }

    public function contentBottomMenuAccess(&$block) {
        /*$support_url = array(
            '/',
        );

        return in_array(lks_url_current_with_prefix(), $support_url);*/

        return true;
    }

    public function footer($block) {
        return lks_render('block-footer');
    }

    public function footerAccess(&$block) {
        return true;
    }

    public function scroll($block) {
        return lks_render('block-scroll');
    }

    public function scrollAccess(&$block) {
        return true;
    }

    public function leftUserPanel($block) {
        $vars = lks_instance_get()->load('\Chovip\Chovip\Chovip')->getUserPanel();

        return lks_render('block-left-userpanel', $vars);
    }

    public function leftUserPanelAccess(&$block) {
        $lks =& lks_instance_get();

        return $lks->response->isUserPanel() && ($lks->request->segment(0) != 'topic' || $lks->request->segment(1) != 'create');
    }

    public function leftArticle($block) {
        return lks_render('block-left-article');
    }

    public function leftArticleAccess(&$block) {
        $lks =& lks_instance_get();

        return $lks->request->segment(0) == 'article' && is_numeric($lks->request->segment(1));
    }

    public function blockEcTopicCreateProcessBar($block) {
        $vars = array(
            'step1' => '',
            'step2' => '',
            'step3' => '',
        );

        $class = 'class="active"';
        switch (lks_instance_get()->request->segment(2)) {
            case 'start':
                $vars['step1'] = $class;
                break;
            
            case 'finalize':
                $vars['step3'] = $class;
                break;

            default:
                $vars['step2'] = $class;
                break;
        }

        return lks_render('block-topic-create-process-bar', $vars);
    }

    public function blockEcTopicCreateProcessBarAccess(&$block) {
        $lks =& lks_instance_get();

        return $lks->request->segment(0) == 'topic' && $lks->request->segment(1) == 'create';
    }

    public function blockEcShopRegisterGuide($block) {
        return lks_render('block-shop-register-guide');
    }

    public function blockEcShopRegisterProcessBar($block) {
        $vars = ['step' => 1];

        switch (lks_instance_get()->request->segment(2)) {
            case 'confirmation':
                $vars['step'] = 2;
                break;

            case 'finalize':
                $vars['step'] = 3;
                break;
        }

        return lks_render('block-shop-register-process-bar', $vars);
    }

    public function blockEcShopRegisterAccess(&$block) {
        $lks =& lks_instance_get();

        return $lks->request->segment(0) == 'shop' 
            && ($lks->request->segment(1) == 'create'
                || in_array($lks->request->segment(2), ['confirmation', 'finalize']));
    }
}