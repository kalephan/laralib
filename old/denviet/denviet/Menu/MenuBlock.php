<?php
namespace Kalephan\Menu;

use Illuminate\Support\Facades\View;

class MenuBlock {
    public function blockMenu($block) {
        $block_class = explode('@', $block->class);
        if (isset($block_class[2])) {
            $menu_group =  $block_class[2];
        }
        else {
            return '';
        }

        $result = '';
        $menu = lks_instance_get()->load('\Kalephan\Menu\MenuEntity')->tree($menu_group);
        if (count($menu)) {
            foreach ($menu as $key => $value) {
                if (strpos($value['path'], '@') !== false) {
                    $value['path'] = explode('/', $value['path']);
                    foreach ($value['path'] as $k => $v) {
                        if (strpos($v, '@') === 0) {
                            $v = substr($v, 1);
                            $value['path'][$k] = $v();
                        }
                    }
                    $menu[$key]['path'] = implode('/', $value['path']);
                }
            }

            $data = array(
                'menu' => $menu,
            );

            $template = 'menu-' . $menu_group;
            if (!View::exists($template)) {
                $template = 'menu';
            }
            $result = lks_render($template, $data);
        }

        return $result;
    }

    public function blockMenuAdminAccess($block) {
        return lks_instance_get()->response->isAdminPanel();
    }
}