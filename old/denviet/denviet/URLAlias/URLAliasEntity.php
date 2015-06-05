<?php
namespace Kalephan\URLAlias;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Cache;

class URLAliasEntity extends EntityAbstract
{

    private $cache_name_alias = __CLASS__ . '-alias-@alias';

    private $cache_name_real = __CLASS__ . '-real-@real';

    public function __config()
    {
        return array(
            '#id' => 'id',
            '#name' => 'urlalias',
            '#class' => '\Kalephan\URLAlias\URLAliasEntity',
            '#title' => lks_lang('URL alias'),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => lks_lang('ID')
                )
                // '#type' => 'hidden',
                ,
                'real' => array(
                    '#name' => 'real',
                    '#title' => lks_lang('URL real')
                )
                // '#type' => 'text',
                ,
                'alias' => array(
                    '#name' => 'alias',
                    '#title' => lks_lang('URL alias')
                )
                // '#type' => 'text',
                
            )
        );
    }

    public function loadReal($path)
    {
        $cache_name = str_replace('@alias', $path, $this->cache_name_alias);
        
        $real = Cache::get($cache_name);
        if ($real !== NULL) {
            $entity = $this->loadEntityWhere([
                'where' => [
                    'alias' => $path
                ]
            ]);
            
            $real = ! empty($entity->real) ? $entity->real : '';
            Cache::forever($cache_name, $real);
        }
        
        return $real;
    }

    public function loadAlias($path)
    {
        $cache_name = str_replace('@real', $path, $this->cache_name_real);
        
        $alias = Cache::get($cache_name);
        if ($alias !== NULL) {
            $entity = $this->loadEntityWhere([
                'where' => [
                    'real' => $path
                ]
            ]);
            
            $alias = ! empty($entity->alias) ? $entity->alias : '';
            Cache::forever($cache_name, $alias);
        }
        
        return $alias;
    }

    public function saveEntity($entity_new, $active_action = false)
    {
        Cache::forget(str_replace('@alias', $entity_new->alias, $this->cache_name_alias));
        Cache::forget(str_replace('@real', $entity_new->real, $this->cache_name_real));
        
        return parent::saveEntity($entity_new, $active_action);
    }

    public function deleteEntity($entity_ids)
    {
        $entity_ids = (array) $entity_ids;
        foreach ($entity_ids as $value) {
            $entity = $this->loadEntity($value);
            
            Cache::forget(str_replace('@alias', $entity->alias, $this->cache_name_alias));
            Cache::forget(str_replace('@real', $entity->real, $this->cache_name_real));
        }
        
        return parent::deleteEntity($entity_ids);
    }

    public function verify($real, $alias, $validate = true)
    {
        // Remove unexpected characters
        $alias = $validate ? lks_str_slug($alias) : $alias;
        
        // Check url alias exists
        $urlalias = $this->loadReal($alias);
        if (! empty($urlalias->alias) && $urlalias->real != $real) {
            $alias .= '-' . strtolower(str_random(4));
            
            // Re-check with new url alias
            $alias = $this->verify($real, $alias, false);
        }
        
        return $alias;
    }

    public function make($real, $alias, $prefix = false, $entity_name = '')
    {
        $prefix = $prefix === false ? ($entity_name ? lks_config_get("urlalias $entity_name prefix", false) : false) : $prefix;
        $prefix = ($prefix === false ? config('lks.urlalias default prefix', '') : $prefix);
        
        // Remove prefix
        $old_prefix = strpos($alias, $prefix . '/');
        if ($old_prefix === 0) {
            $alias = substr($alias, $old_prefix + strlen($prefix) + 1);
        }
        
        $alias = trim($prefix . '/' . $alias, '/');
        $alias = $this->verify($real, $alias);
        
        // Save to db
        if ($urlalias = $this->loadAlias($real)) {
            // Update a new url alias for old url real
            if ($urlalias != $alias) {
                $urlalias = $alias;
                $this->saveEntity($urlalias);
            }
        }         // Save a new url alias for new url real
        else {
            $urlalias = new \stdClass();
            $urlalias->real = $real;
            $urlalias->alias = $alias;
            $this->saveEntity($urlalias);
        }
    }
}