<?php namespace Kalephan\LKS;

/*use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\JSMinFilter;*/
use Illuminate\Html\HtmlFacade as HTML;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class Asset {
    private $theme = '';
    private $css = array(
        'config' => [],
        'custom' => [],
    );
    private $js = array(
        'config' => [],
        'custom' => [],
        'inline' => [],
        'settings' => [],
    );

    public function css() {
        $this->_assetConfig('css');
        return $this->_cssBuild();
    }

    public function cssAdd($data, $type = 'custom') {
        $this->_assetAdd($data, $type, 'css');
    }

    public function js() {
        $this->_assetConfig('css');
        return $this->_jsBuild();
    }

    public function jsAdd($data, $type = 'custom') {
        $this->_assetAdd($data, $type, 'js');
    }

    private function _assetConfig($type) {
        // Config was loaded.
        if ($this->theme) {
            return;
        }

        $config = config('lks.theme_default') . '/config.php';

        if (!file_exists($config)) {
            throw new \Exception('config.php file is not exists on theme folder.');
        }

        $config = require $config;

        try {
            $this->theme = $config['name'];
            $this->css['config'] = $config['css'];
            $this->js['config'] = $config['js'];
        }
        catch(\Exception $e) {
            throw new \Exception('Theme config is invalid format.');
        }
    }

    private function _assetAdd($data, $type, $asset) {
        if (is_string($data)) {
            $data = (array) $data;
        }

        $this->{$asset}[$type] = lks_array_merge_deep($this->{$asset}[$type], $data);
    }

    private function _assetBuild($asset_files, $type) {
        if (empty($asset_files)) {
            return '';
        }

        // Compress asset files
        /*$result = [];
        if (config("lks.asset_compress_$type", false)) {
            $asset_group = $this->_assetGroup($asset_files);

            foreach ($asset_group as $key => $value) {
                $key = explode('|', $key);

                if ((!isset($key[1]) || $key[1] != 'no-compress')
                ) {
                    $file_name = '_' . md5(mt_rand() . microtime()) . ".$type";
                    $this->_assetCompress($file_name, $key[0], $value, $type);
                    $file_name = $key[0] . '/' . $file_name;
                } else {
                    $file_name = reset($value);
                }

                switch ($type) {
                    case 'css':
                        $result[] = '<link href="' . $file_name . '" rel="stylesheet">';
                        break;

                    case 'js':
                        $result[] = '<script type="text/javascript" src="' . $file_name . '"></script>';
                        break;
                }
            }
        }*/

        foreach ($asset_files as $value) {
            switch ($type) {
                case 'css':
                    if (is_string($value)) {
                        $value = ['href' => $value];
                    }

                    if (!is_array($value)) {
                        throw new \Exception('Theme css config is invalid format.');
                    }

                    $value['rel'] = 'stylesheet';

                    $result[] = '<link' . HTML::attributes($value) . '>';
                    break;

                case 'js':
                    if (is_string($value)) {
                        $value = ['src' => $value];
                    }

                    if (!is_array($value)) {
                        throw new \Exception('Theme js config is invalid format.');
                    }

                    $value['type'] = 'text/javascript';

                    $result[] = '<script' . HTML::attributes($value) . '></script>';
                    break;
            }
        }

        return implode('', $result);
    }

    private function _cssBuild() {
        // Get CSS in config file
        $css_config = '';
        if ($this->css['config']) {
            $cache_name = lks_cache_name(__METHOD__ . '-config-') . $this->theme;
            if ($cache = Cache::get($cache_name)) {
                $css_config = $cache;
            }
            else {
                $css_config = $this->_assetBuild($this->css['config'], 'css');
                Cache::forever($cache_name, $css_config);
            }
        }

        // Get CSS was added by Asset::cssAdd() funtion
        $css_custom = '';
        if ($this->css['custom']) {
            $cache_name = lks_cache_name(__METHOD__ . '-custom-') . $this->theme . '-' . Route::getCurrentRoute()->getPath();
            if ($cache = Cache::get($cache_name)) {
                $css_custom = $cache;
            }
            else {
                $css_custom = $this->_assetBuild($this->css['custom'], 'css');
                Cache::forever($cache_name, $css_custom);
            }
        }

        return $css_config . $css_custom;
    }

    private function _jsBuild() {
        // jQuery && LKS first
        $cache_name = lks_cache_name(__METHOD__ . '-first-') . $this->theme;
        if ($cache = Cache::get($cache_name)) {
            $js_first = $cache;
            $this->_jsBuildFirst(true);
        }
        else {
            $js_first = $this->_jsBuildFirst();
            Cache::forever($cache_name, $js_first);
        }

        $js_settings = '';
        if ($this->js['settings']) {
            $js_settings = $this->_jsBuildSetting();
        }

        // Get JS in config file
        $js_config = '';
        if ($this->js['config']) {
            $cache_name = lks_cache_name(__METHOD__ . '-config-') . $this->theme;
            if ($cache = Cache::get($cache_name)) {
                $js_config = $cache;
            }
            else {
                $js_config = $this->_assetBuild($this->js['config'], 'js');
                Cache::forever($cache_name, $js_config);
            }
        }

        // Get JS was added by Asset::jsAdd() funtion
        $js_custom = '';
        if ($this->js['custom']) {
            $cache_name = lks_cache_name(__METHOD__ . '-custom-') . $this->theme . Route::getCurrentRoute()->getPath();
            if ($cache = Cache::get($cache_name)) {
                $js_custom = $cache;
            }
            else {
                $js_custom = $this->_assetBuild($this->js['custom'], 'js');
                Cache::forever($cache_name, $js_custom);
            }
        }

        $js_inline = '';
        if ($this->js['inline']) {
            $js_inline = '<script type="text/javascript">' . implode('', $value) . '</script>';
        }

        return $js_first . $js_settings . $js_config . $js_custom . $js_inline;
    }

    private function _jsBuildSetting() {
        return count($this->js['settings']) ? "<script type='text/javascript'>jQuery.extend(LKS.settings, " . json_encode(array_filter($this->js['settings'])) . ");</script>" : '';
    }

    private function _jsBuildFirst($remove_only = false) {
        $result = '';

        if (isset($this->js['config']['libraries.jquery'])) {
            if (!$remove_only) {
                $result .= $this->_assetBuild([$this->js['config']['libraries.jquery']], 'js');
            }

            unset($this->js['config']['libraries.jquery']);
        }

        if (isset($this->js['config']['lks.lks'])) {
            if (!$remove_only) {
                $result .= $this->_assetBuild([$this->js['config']['libraries.lks']], 'js');
            }

            unset($this->js['config']['libraries.lks']);
        }

        return $result;
    }

    /*private function _assetGroup($files) {
        $assets = [];
        foreach ($files as $key => $value) {
            $key = explode('|', $key);

            if (substr($value, 0, 2) == '//'
                || substr($value, 0, 7) == 'http://'
                || substr($value, 0, 8) == 'https://'
            ) {
                $key[1] = 'no-compress';
            }
            $key[1] = isset($key[1]) ? $key[1] : '';

            $assets[substr($value, 0, strrpos($value, '/')) . '|' . $key[1]][] = $value;
        }

        return $assets;
    }

    private function _assetCompress($file_name, $file_path, $asset_files, $type) {
        $files = [];
        foreach ($asset_files as $value) {
            $files[] = new FileAsset(public_path() . $value);
        }

        switch ($type) {
            case 'js':
                $assets = new AssetCollection($files, array(
                    new JSMinFilter(),
                ));
                break;

            case 'css':
                $assets = new AssetCollection($files, array(
                    new CssMinFilter(),
                ));
                break;
        }
        $assets->setTargetPath($file_name);

        $am = new AssetManager();
        $am->set('assets', $assets);

        $writer = new AssetWriter(public_path() . $file_path);
        $writer->writeManagerAssets($am);
    }*/
}
