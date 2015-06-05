<?php
namespace Kalephan\Menu;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Cache;

class MenuEntity extends EntityAbstract
{

    public function __config()
    {
        return array(
            '#id' => 'id',
            '#name' => 'menus',
            '#class' => '\Kalephan\Menu\MenuEntity',
            '#title' => lks_lang('menu'),
            '#order' => array(
                'weight' => 'asc',
                'title' => 'asc'
            ),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    /*'#title' => lks_lang('ID'),
                    '#type' => 'hidden',*/
                ),
                'code' => array(
                    '#name' => 'code',
                    /*'#title' => lks_lang('Mã'),
                    '#type' => 'text',
                    '#list_hidden' => 1,*/
                ),
                'title' => array(
                    '#name' => 'title',
                    /*'#title' => lks_lang('Tiêu đề'),
                    '#type' => 'text',*/
                ),
                'parent' => array(
                    '#name' => 'parent',
                    /*'#title' => lks_lang('Menu cha'),
                    '#type' => 'select',
                    '#reference' => array(
                        'name' => 'menutree',
                        'class' => '\Kalephan\Core',
                    ),
                    '#attributes' => array(
                        'size' => 10,
                    ),
                    '#list_hidden' => 1,*/
                ),
                'group' => array(
                    '#name' => 'group',
                    /*'#title' => lks_lang('Nhóm'),
                    '#type' => 'text',
                    '#validate' => 'required',*/
                ),
                'path' => array(
                    '#name' => 'path',
                    /*'#title' => lks_lang('URL'),
                    '#type' => 'text',
                    '#validate' => 'required',*/
                ),
                'anchor_attributes' => array(
                    '#name' => 'anchor_attributes',
                    /*'#title' => lks_lang('Anchor Attributes'),
                    '#type' => 'textarea',
                    '#list_hidden' => 1,*/
                ),
                'li_attributes' => array(
                    '#name' => 'li_attributes',
                    /*'#title' => lks_lang('LI Attributes'),
                    '#type' => 'textarea',
                    '#list_hidden' => 1,*/
                ),
                'weight' => array(
                    '#name' => 'weight',
                    /*'#title' => lks_lang('Xếp hạng'),
                    '#type' => 'select',
                    '#options' => lks_form_options_make_weight(),
                    '#validate' => 'required|numeric|between:-99,99',
                    '#fast_edit' => 1,*/
                ),
                'active' => array(
                    '#name' => 'active',
                    /*'#title' => lks_lang('Kích hoạt'),
                    '#type' => 'radios',
                    '#options' => array(
                        1 => lks_lang('Bật'),
                        0 => lks_lang('Tắt'),
                    ),
                    '#validate' => 'required|numeric|between:0,1'*/
                )
            )
        );
    }

    public function loadEntityAllByGroup($group, $attributes = [])
    {
        if (! isset($attributes['where'])) {
            $attributes['where'] = [];
        }
        $attributes['where']['group'] = $group;
        
        return $this->loadEntityAll($attributes);
    }
    
    // Get tree menu of a group
    public function tree($group)
    {
        $cache_name = __METHOD__ . $group;
        if ($cache = Cache::get($cache_name)) {
            return $cache;
        }
        
        $menu = $this->loadEntityAllByGroup($group);
        
        $result = [];
        if (count($menu)) {
            foreach ($menu as $key => $value) {
                $menu[$key] = $this->loadEntity($value->id);
            }
            $result = lks_tree_build($menu);
        }
        
        Cache::forever($cache_name, $result);
        return $result;
    }
}