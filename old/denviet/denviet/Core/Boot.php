<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Boot
{

    private static $instance;

    private $language;

    public function __construct()
    {
        self::$instance = &$this;
    }

    public static function &getInstance()
    {
        return self::$instance;
    }

    public function bootstrap($request_type = 'get')
    {
        $this->language = config('lks.language default', 'original');
        
        // $event = $this->load('\Kalephan\Event\EventEntity');
        
        $this->request = new Request();
        $this->response = new Response();
        $this->asset = new Asset();
        
        $path = $this->request->route();
        $route = $this->load('\Kalephan\Core\Route');
        $route = $route->getRouteItem($this->request->segment(), $path);
        
        if (! $route) {
            Log::notice('Page not found: ' . implode('/', $this->request->segment()));
            App::abort(404);
        }
        
        $args = lks_menu_arguments_get($this->request->segment(), $route['arguments']);
        
        // Check access
        if (! empty($route['access'])) {
            $access = lks_access($route['access'], Auth::id(), $args);
            if (! $access) {
                App::abort(403);
            } elseif ($access !== true) {
                return $access;
            }
        }
        
        event('lks.init', []);
        
        // Process form
        if ($request_type == 'post') {
            // Check CSRF
            if (Session::token() !== Input::get('_token')) {
                $this->response->addMessage(lks_lang('Biểu mẫu này đã hết hạn. Xin thử lại.'), 'error');
            } else {
                $return = Form::submit();
                
                // Form redirect
                if ($return !== true && $return !== false) {
                    return $return;
                }
            }
        }
        
        // Add Default title
        if (empty($this->response->getTitle()) && $route['title']) {
            $this->response->addTitle($route['title']);
        }
        
        event('lks.controllerBefore', []);
        
        // Run controller and return for redirect if needed
        $segment = explode('@', $route['class']);
        if ($return = call_user_func_array([
            $this->load($segment[0]),
            $segment[1]
        ], $args)) {
            return $return;
        }
        
        event('lks.page_build', []);
        
        return $this->response->output();
    }

    public function load($class, $name = '')
    {
        $name = trim($name ? $name : $class, '\\');
        
        if (! isset($this->$name)) {
            $this->$name = new $class();
        }
        
        return $this->$name;
    }

    public function getLanguage()
    {
        return $this->language;
    }
}