<?php
if (! function_exists('lks_form_options_make_weight')) {

    function lks_form_options_make_weight()
    {
        $options = [];
        for ($i = - 99; $i <= 99; $i ++) {
            $options[$i] = $i;
        }
        
        return $options;
    }
}

if (! function_exists('lks_paganization')) {

    function lks_paganization($current, $sum)
    {
        $prev = max(1, $current - 1);
        $next = min($sum, $current + 1);
        
        $data = array(
            'current' => $current,
            'first' => 1,
            'prev' => $prev,
            'next' => $next,
            'last' => $sum,
            'sum' => $sum,
            'item' => config('lks.pagination items', 5)
        );
        
        return lks_render('pagination', $data);
    }
}

// Build tree children
if (! function_exists('lks_tree_build')) {

    function lks_tree_build(array $list, $parent = null, $config = [])
    {
        $parent = $parent === 0 ? null : $parent;
        $result = [];
        
        $key_id = ! empty($config['id']) ? $config['id'] : 'id';
        $key_parent = ! empty($config['parent']) ? $config['parent'] : 'parent';
        
        foreach ($list as $key => $value) {
            $value = is_object($value) ? lks_object_to_array($value) : $value;
            if ($value[$key_parent] == $parent) {
                // unset($list[$key]);
                $result[$value[$key_id]] = $value;
                $result[$value[$key_id]]['#children'] = lks_tree_build($list, $value[$key_id]);
            }
        }
        
        return $result;
    }
}

if (! function_exists('lks_anchor')) {

    function lks_anchor($url, $title, $attributes = [])
    {
        return '<a href="' . lks_url($url) . '" ' . HTML::attributes($attributes) . '>' . $title . '</a>';
    }
}

if (! function_exists('chovip_anchor_login')) {

    function lks_anchor_login($url, $title, $attributes = [])
    {
        if (Auth::id()) {
            return lks_anchor($url, $title, $attributes);
        }
        
        return lks_template_anchor_bootstrap_modal("modal/user/login?d=$url", $title, $attributes);
    }
}

if (! function_exists('lks_user')) {

    function lks_user()
    {
        if ($user = lks_static(__FUNCTION__)) {
            return $user;
        }
        
        if ($userid = Auth::id()) {
            $user = lks_instance_get()->load('\Kalephan\User\UserEntity')->loadEntity($userid);
        } else {
            $user = new \stdClass();
            $user->id = 0;
            $user->role[1] = 1; // Anonymous user role
        }
        
        return lks_static(__FUNCTION__, $user);
    }
}

if (! function_exists('lks_static')) {

    function lks_static($key, $default_value = null)
    {
        static $fw_static;
        
        // $fw_static[$key] can is 'false'/0 but not 'null'
        if (! isset($fw_static[$key]) || $fw_static[$key] === null) {
            $fw_static[$key] = $default_value;
        }
        
        return $fw_static[$key];
    }
}

if (! function_exists('lks_variable_get')) {

    function lks_variable_get($key, $default = null)
    {
        $cache_name = __METHOD__ . $key;
        $cache = Cache::get($cache_name);
        if ($cache !== NULL) {
            return $cache;
        }
        
        $hasTable = true;
        $hasTable_cache_name = 'hasTable' . __METHOD__ . 'variable';
        $hasTable_cache = Cache::get($hasTable_cache_name);
        if ($hasTable_cache === NULL) {
            if (Schema::hasTable('variables')) {
                Cache::forever($hasTable_cache_name, $hasTable);
            } else {
                $hasTable = false;
            }
        }
        
        if ($hasTable) {
            $default = VariableModel::get($key, $default);
            lks_cache_set($cache_name, $default);
        }
        
        return $default;
    }
}

if (! function_exists('lks_config_get')) {

    function lks_config_get($key, $default = null, $nocache = false)
    {
        $cache_name = __METHOD__ . $key;
        if (! $nocache) {
            $cache = Cache::get($cache_name);
            if ($cache !== NULL) {
                return $cache;
            }
        }
        
        $hasTable = true;
        $hasTable_cache_name = 'hasTable' . __METHOD__ . 'variable';
        $hasTable_cache = Cache::get($hasTable_cache_name);
        if ($hasTable_cache === NULL) {
            if (Schema::hasTable('variables')) {
                Cache::forever($hasTable_cache_name, $hasTable);
            } else {
                $hasTable = false;
            }
        }
        
        if ($hasTable) {
            $default = VariableModel::get($key, $default);
            Cache::forever($cache_name, $default);
        }
        
        return $default;
    }
}

if (! function_exists('lks_variable_set')) {

    function lks_variable_set($key, $value)
    {
        return VariableModel::set($key, $value);
    }
}

if (! function_exists('lks_config_set')) {

    function lks_config_set($key, $value)
    {
        return VariableModel::set($key, $value);
    }
}

if (! function_exists('lks_redirect')) {

    function lks_redirect($url = '')
    {
        $url = trim(lks_redirect_get_path($url), '/');
        $url = $url ? $url : '/';
        
        return Redirect::to($url);
    }
}

if (! function_exists('lks_url')) {

    function lks_url($path = '', $query = '', $attributes = [], $secure = false)
    {
        $path = $path ? $path : lks_url_current_with_prefix();
        $path .= $query ? "?$query" : '';
        
        $http = '';
        $data = array(
            'path' => &$path,
            'http' => &$http
        );
        event('helpers.makeURLAlter', $data);
        $path = $data['path'];
        $http = $data['http'];
        
        return URL::to(trim("$http/$path", '/'), $attributes, $secure);
    }
}

if (! function_exists('lks_file_get_filename')) {

    function lks_file_get_filename($file, $path)
    {
        if (! File::isDirectory($path)) {
            File::makeDirectory($path);
        }
        
        // Get file name
        $file_extension = $file->getClientOriginalExtension();
        $file_name = $file->getClientOriginalName();
        $file_name = lks_str_slug(str_replace($file_extension, '', $file_name));
        $result = "$file_name.$file_extension";
        while (File::exists("$path/$result")) {
            $result = "$file_name-" . strtolower(Str::random(4)) . ".$file_extension";
        }
        
        return $result;
    }
}

if (! function_exists('lks_mail')) {

    function lks_mail($email, $subject, $body, $template = 'email')
    {
        $data = array(
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
            'template' => $template
        );
        
        if (config('lks.lks queue use', 0)) {
            Queue::push('\Kalephan\Core\Mail@send', $data);
        } else {
            Mail::send(null, $data);
        }
    }
}

if (! function_exists('lks_entity_contextual_link_check_access')) {

    function lks_entity_contextual_link_check_access($link)
    {
        /*
         * $lks =& lks_instance_get();
         *
         * $link_real = $lks->request->parseURI($link);
         * $link_real = $link_real['url'];
         *
         * $route = $lks->load('\Kalephan\Core\Route');
         * $menu = $route->getRouteItem($link_real);
         *
         * //k($link_real, $menu);
         *
         * if ($menu) {
         * $arguments = lks_menu_arguments_get($link_real, $menu->arguments);
         *
         * if (empty($menu->access) || lks_access($menu->access, Auth::id(), $arguments)) {
         * return true;
         * }
         * }
         *
         * return false;
         */
        return true;
    }
}

if (! function_exists('lks_entity_contextual_link_get')) {

    function lks_entity_contextual_link_get($entity, $structure)
    {
        $item = [];
        
        $lks = & lks_instance_get();
        
        if ($lks->response->isAdminPanel() && ! empty($structure['#action_links']['read']) && (! isset($entity->active) || $entity->active == 1)) {
            $link = str_replace('%', $entity->{$structure->id}, $structure['#action_links']['read']);
            
            if (lks_entity_contextual_link_check_access($link)) {
                $item['read'] = lks_anchor($link, lks_lang('Xem'));
            }
        }
        
        if ($lks->response->isAdminPanel() && ! empty($structure['#action_links']['preview']) && (isset($entity->active) && $entity->active != 1)) {
            $link = str_replace('%', $entity->{$structure->id}, $structure['#action_links']['preview']);
            
            if (lks_entity_contextual_link_check_access($link)) {
                $item['preview'] = lks_anchor($link, lks_lang('Xem trước'));
            }
        }
        
        if (! empty($structure['#action_links']['update'])) {
            $link = str_replace('%', $entity->{$structure->id}, $structure['#action_links']['update']);
            
            if (lks_entity_contextual_link_check_access($link)) {
                $item['update'] = lks_anchor($link, lks_lang('Sửa'));
            }
        }
        
        if (! empty($structure['#action_links']['delete'])) {
            $link = str_replace('%', $entity->{$structure->id}, $structure['#action_links']['delete']);
            
            if (lks_entity_contextual_link_check_access($link)) {
                $item['delete'] = lks_anchor($link, lks_lang('Xóa'));
            }
        }
        
        if (! empty($structure['#action_links']['clone'])) {
            $link = str_replace('%', $entity->{$structure->id}, $structure['#action_links']['clone']);
            
            if (lks_entity_contextual_link_check_access($link)) {
                $item['clone'] = lks_anchor($link, lks_lang('Sao chép'));
            }
        }
        
        if (! empty($structure['#action_links']['active']) && (isset($entity->active) && $entity->active != 1)) {
            $link = str_replace('%', $entity->{$structure->id}, $structure['#action_links']['active']);
            
            if (lks_entity_contextual_link_check_access($link)) {
                $item['active'] = lks_anchor($link, lks_lang('Phê duyệt')); // Kích hoạt
            }
        }
        
        if (! empty($structure['#action_links']['approve']) && (isset($entity->approve) && $entity->approve)) {
            $link = str_replace('%', $entity->{$structure->id}, $structure['#action_links']['approve']);
            
            if (lks_entity_contextual_link_check_access($link)) {
                $item['approve'] = lks_anchor($link, lks_lang('Phê duyệt'));
            }
        }
        
        return $item;
    }
}

if (! function_exists('lks_template_anchor_bootstrap_modal')) {

    function lks_template_anchor_bootstrap_modal($url, $title, $attributes = [], $target = "#myModal")
    {
        return '<a ' . HTML::attributes($attributes) . ' data-toggle="modal" data-target="' . $target . '" href="' . lks_url($url) . '">' . $title . '</a>';
    }
}

function lks_style($path, $style = 'normal', $attributes = [])
{
    if (! $path) {
        return '';
    }
    
    $lks = lks_instance_get();
    $img_site = Config::get('lks.site_urls_cdn', '');
    
    if (substr($path, 0, 2) != '//' && substr($path, 0, 7) != 'http://' && substr($path, 0, 8) != 'https://') {
        $image = $lks->load('\Kalephan\Style\StyleEntity');
        $image = $image->image($path, $style);
    } elseif ($img_site && substr($path, 0, strlen($img_site)) == $img_site && config('lks.cdn image style make', 0)) {
        $key = substr(md5(mt_rand()), 0, 7);
        $img = $lks->load('\Kalephan\Style\StyleEntity')->loadEntity($style);
        
        if ($img && $image_data = @file_get_contents(Config::get('lks.site_urls_cdn_generate', '') . '?path=' . substr($path, strlen($img_site)) . "&style=$style&width=" . $img->width . "&height=" . $img->height . "&type=" . $img->type . "&is_upsize=" . $img->is_upsize . "&key=$key&code=" . md5($key . Config::get('app.key')))) {
            $image_data = explode('|', $image_data);
            $image = [];
            $image['path'] = ! empty($image_data[0]) ? $img_site . $image_data[0] : '';
            $image['width'] = ! empty($image_data[1]) ? $image_data[1] : '';
            $image['height'] = ! empty($image_data[2]) ? $image_data[2] : '';
        }
    }
    
    if (isset($image) && isset($image['path'])) {
        $attributes['width'] = isset($attributes['width']) ? $attributes['width'] : (isset($image['width']) ? $image['width'] : '');
        $attributes['height'] = isset($attributes['height']) ? $attributes['height'] : (isset($image['height']) ? $image['height'] : '');
        
        $path = $image['path'];
    }
    
    if (config('lks.image lazy load', 1)) {
        $path = 'data-original="' . $path . '"';
        
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' ' : '';
        $attributes['class'] .= 'lazy loading';
    } else {
        $path = 'src="' . $path . '"';
    }
    
    return "<img $path " . HTML::attributes(array_filter($attributes)) . " />";
}

/*
function lks_flush_cache_view() {
    $cachedViewsDirectory = app('path.storage').'/views/';
    $files = glob($cachedViewsDirectory.'*');

        foreach($files as $file) {
            if(is_file($file)) {
                @unlink($file);
            }
        }
}

if (!function_exists('template_tree_build')) {
    function template_tree_build($tree) {
        $result = [];

        $tree = lks_object_to_array($tree);

        foreach ($tree as $key => $value) {
            if (!empty($value['#parent'])) {
                if (is_array($value['#parent'])) {
                    $parent = reset(array_keys($value['#parent']));
                }
                else {
                    $parent = $value['#parent'];
                }
                unset($value['#parent']);

                if (!isset($result[$parent]['#children'])) {
                    $result[$parent]['#children'] = [];
                }

                $result[$parent]['#children'][] = $value;
            }
            else {
                if (isset($value['#parent'])) {
                    unset($value['#parent']);
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }
}

if (!function_exists('template_tree_build_option')) {
    function template_tree_build_option($tree, $parent = 0, $load_children = true, $level = 0) {
        $result = [];
        $tree = template_tree_build($tree);


        if ($parent) {
            if(!empty($tree[$parent]['#children'])) {
                $tree = $tree[$parent]['#children'];
            }
            else {
                return $result;
            }
        }

        $prefix = '---';
        foreach ($tree as $key => $value) {
            if (isset($value['#title'])) {
                $i = $level;
                while ($i > 0) {
                    $value['#title'] = $prefix . $value['#title'];
                    $i--;
                }
            }

            $result[$key] = $value;

            if ($load_children) {
                if (isset($value['#children']) && count($value['#children'])) {
                    $result = array_merge($result, template_tree_build_option($value['#children'], $parent, $load_children, $level+1));
                    $level-1;
                }
            }
        }

        return $result;
    }
}
*/
