<?php
namespace Kalephan\Block;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class BlockEntity extends EntityAbstract
{

    public function __config()
    {
        // Get Regions of Theme_default and Theme_admin
        /*
         * $config = require lks_instance_get()->response->getThemeConfig('frontend');
         * $regions = $config['regions'];
         * $config = require lks_instance_get()->response->getThemeConfig('backend');
         * $regions += $config['regions'];
         * asort($regions);
         */
        return array(
            '#id' => 'id',
            '#name' => 'blocks',
            '#class' => '\Kalephan\Block\BlockEntity',
            '#title' => lks_lang('Block'),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    /*'#title' => lks_lang('ID'),
                    '#type' => 'hidden',*/
                ),
                'delta' => array(
                    '#name' => 'delta',
                    /*'#title' => lks_lang('Nội dung'),
                    '#type' => 'textarea',
                    '#rte_enable' => 1,
                    '#list_hidden' => 1,*/
                ),
                'title' => array(
                    '#name' => 'title',
                    /*'#title' => lks_lang('Tiêu đề'),
                    '#type' => 'text',*/
                ),
                'cache' => array(
                    '#name' => 'cache',
                    /*'#title' => lks_lang('Kiểu Cache'),
                    '#type' => 'select',
                    '#options' => array(
                        '' => lks_lang('Không Cache'),
                        'full' => lks_lang('Cache toàn bộ'),
                        'page' => lks_lang('Cache bởi URL'),
                        'route' => lks_lang('Cache bởi Route'),
                        'user' => lks_lang('Cache bởi User'),
                        'page-user' => lks_lang('Cache bởi URL & User'),
                        'route-user' => lks_lang('Cache bởi Route & User'),
                    ),
                    '#validate' => 'required',*/
                ),
                'region' => array(
                    '#name' => 'region',
                    /*'#title' => lks_lang('Region'),
                    '#type' => 'select',
                    '#options' => $regions,
                    '#validate' => 'required',*/
                ),
                'class' => array(
                    '#name' => 'class',
                    /*'#title' => lks_lang('Lớp'),
                    '#type' => 'text',
                    '#list_hidden' => 1,*/
                ),
                'access' => array(
                    '#name' => 'access',
                    /*'#title' => lks_lang('Quyền truy cập'),
                    '#type' => 'text',
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
                    '#validate' => 'required|numeric|between:0,1',
                    '#default' => 1,*/
                )
            )
        );
    }

    function loadEntityAll($attr = [])
    {
        $cache_name = __METHOD__ . serialize($attr);
        if ($cache = Cache::get($cache_name)) {
            return $cache;
        }
        
        $attr['select'] = '*';
        $block_raw = parent::loadEntityAll($attr);
        
        $blocks = [];
        if (count($block_raw)) {
            foreach ($block_raw as $block) {
                $regions = explode('|', $block->region);
                if (count($regions)) {
                    foreach ($regions as $value) {
                        $blocks[$value][$block->id] = $block;
                    }
                }
            }
        }
        
        Cache::forever($cache_name, $blocks);
        return $blocks;
    }

    public function run($block)
    {
        if (self::_checkBlocksVisible($block)) {
            if ($block->cache) {
                $cache_name = __METHOD__ . $block->id . Config::get('lks.site', 'frontend');
                switch ($block->cache) {
                    case 'full':
                        $cache_name .= "full";
                        break;
                    
                    case 'page':
                        $cache_name .= "page-" . md5(lks_url_current_with_prefix());
                        break;
                    
                    case 'route':
                        $cache_name .= "route-" . md5(lks_instance_get()->request->route());
                        break;
                    
                    case 'user':
                        $cache_name .= "user-" . Auth::id();
                        break;
                    
                    case 'page-user':
                        $cache_name .= "page-user-" . md5(lks_url_current_with_prefix()) . Auth::id();
                        break;
                    
                    case 'route-user':
                        $cache_name .= "menu-route-" . md5(lks_instance_get()->request->route()) . Auth::id();
                        break;
                }
                
                $cache = Cache::get($cache_name);
                if ($cache) {
                    return $cache;
                }
            }
            
            if (! empty($block->class)) {
                $segment = explode('@', $block->class);
                $block_content = call_user_func_array([
                    lks_instance_get()->load($segment[0]),
                    $segment[1]
                ], [
                    $block
                ]);
            } else {
                $block_content = '';
            }
            
            if ($block->cache) {
                lks_cache_set($cache_name, $block_content);
            }
            
            return $block_content;
        }
        
        return '';
    }

    private function _checkBlocksVisible(&$block)
    {
        if (empty($block->access)) {
            return true;
        }
        
        $segment = explode('@', $block->access);
        $data = [
            'block' => &$block
        ];
        $response = call_user_func_array([
            lks_instance_get()->load($segment[0]),
            $segment[1]
        ], $data);
        $block = $data['block'];
        
        return $response;
    }
}