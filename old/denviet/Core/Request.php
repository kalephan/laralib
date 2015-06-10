<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\Request as LaravelRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Request
{

    private $path_prefix;

    private $data = array(
        'route' => '',
        'prefix' => '',
        'url' => '',
        'full' => '',
        'original' => '',
        'segment' => [],
        'filter' => [],
        'query' => []
    );

    public function __construct()
    {
        $this->path_prefix = config('lks.request prefix paths', [
            'modal',
            'ajax',
            'json',
            'esi',
            'admin',
            'up',
            'iframe'
        ]);
        $this->_parseURI();
    }

    private function _getFilter(&$uri, &$filter = [])
    {
        if (strpos($uri, '?')) {
            $uri = explode('?', $uri);
            $filter = isset($uri[1]) ? explode('&', $uri[1]) + $filter : $filter;
            $uri = $uri[0];
        }
    }

    public function parseURI($uri, $filter = [])
    {
        $result = [];
        $result['full'] = $uri;
        
        $this->_getFilter($uri, $filter);
        $result['original'] = $uri;
        
        // Event fire 'request.uriAlter'
        $data = [
            'uri' => &$uri
        ];
        event('request.uriAlter', $data);
        $uri = $data['uri'];
        
        $this->_getFilter($uri, $filter);
        
        $uri = explode('/', mb_strtolower($uri));
        
        // Get Prefix
        $result['prefix'] = '';
        if (in_array($uri[0], $this->path_prefix)) {
            $result['prefix'] = array_shift($uri);
        }
        
        $result['segment'] = $uri;
        $result['segment'] = count($result['segment']) ? $result['segment'] : array(
            '',
            ''
        );
        
        $result['url'] = implode('/', $uri);
        $result['url'] = $result['url'] ? $result['url'] : '/';
        $result['filter'] = '';
        if (isset($filter['f'])) {
            $result['filter'] = $this->_parseFilter($filter['f']);
            unset($filter['f']);
        }
        
        $result['query'] = $this->_parseQuery($filter);
        
        return $result;
    }

    private function _parseURI()
    {
        $result = $this->parseURI(LaravelRequest::path(), LaravelRequest::query());
        
        $this->data['prefix'] = $result['prefix'];
        $this->data['segment'] = $result['segment'];
        $this->data['url'] = $result['url'];
        $this->data['full'] = $result['full'];
        $this->data['original'] = $result['original'];
        $this->data['filter'] = $result['filter'];
        $this->data['query'] = $result['query'];
    }

    public function filter($index = 'all', $default = false)
    {
        return $this->_getDataIndex('filter', $index, $default);
    }

    private function _getDataIndex($type, $index = 'all', $default = false)
    {
        // Check this before for $index === 0
        if (isset($this->data[$type][$index])) {
            return $this->data[$type][$index];
        } elseif ($index == 'all') {
            return $this->data[$type];
        }
        
        return $default;
    }

    private function _parseFilter($filter)
    {
        $cache_name = 'LKS-request-parsefilter' . Session::getId();
        $result = Cache::get($cache_name, []);
        
        $filter = explode('|', $filter);
        if (count($filter)) {
            foreach ($filter as $value) {
                $value = explode('~', $value);
                if (isset($value[0]) && isset($value[1])) {
                    $result[lks_str_slug($value[0])] = $value[1];
                }
            }
        }
        
        lks_cache_set($cache_name, $result);
        return $result;
    }

    private function _parseQuery($query)
    {
        foreach ($query as $key => $value) {
            if (is_string($value)) {
                $value = lks_str_slug(trim($value, '/'));
            }
            $query[$key] = $value;
        }
        return $query;
    }

    public function segment($index = 'all', $default = false)
    {
        return $this->_getDataIndex('segment', $index, $default);
    }

    public function prefix()
    {
        return $this->data['prefix'];
    }

    public function route()
    {
        return $this->data['route'];
    }

    public function url()
    {
        return $this->data['url'];
    }

    public function query($index = 'all', $default = false)
    {
        return $this->_getDataIndex('query', $index, $default);
    }

    public function addCookie($key, $value)
    {
        setcookie($key, $value, time() + 60 * 60 * 24 * 30, '/');
    }
    
    /*
     * public function urlFull() {
     * return $this->data['url_full'];
     * }
     */
}