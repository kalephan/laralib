<?php
namespace Kalephan\User;

use Kalephan\LKS\EntityAbstract;
use Kalephan\LKS\EntityControllerTrait;
use Illuminate\Support\Facades\Cache;

class RoleEntity extends EntityAbstract{
    use EntityControllerTrait;

    public function __config() {
        return array(
            '#name' => 'roles',
            '#class' => '\Kalephan\User\RoleEntity',
            '#title' => lks_lang('Vai trò'),
            '#id' => 'id',
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    /*'#title' => lks_lang('ID'),
                    '#type' => 'hidden',*/
                ),
                'title' => array(
                    '#name' => 'title',
                    /*'#title' => lks_lang('Tiêu đề vai trò'),
                    '#type' => 'text',
                    '#validate' => 'required|max:80'*/
                ),
                'active' => array(
                    '#name' => 'active',
                    /*'#title' => lks_lang('Kích hoạt'),
                    '#type' => 'radios',
                    '#options' => array(
                        1 => lks_lang('Bật'),
                        0 => lks_lang('Tắt'),
                    ),
                    '#default' => 0,
                    '#validate' => 'required|numeric|between:0,1',*/
                ),
                'weight' => array(
                    '#name' => 'weight',
                    /*'#title' => lks_lang('Xếp hạng'),
                    '#type' => 'select',
                    '#options' => lks_form_options_make_weight(),
                    '#default' => 0,
                    '#validate' => 'required|numeric|between:-99,99',
                    '#fast_edit' => 1,*/
                ),
            ),
        );
    }

    /*function permissions_form($role, $permissions, $access) {
        $form = [];
        $form_values = [];
        $field = array(
            '#type' => 'checkbox',
        );

        foreach ($permissions as $permission) {
            $perms_and = explode('&&', $permission->name);
            $perms = [];
            foreach ($perms_and as $perms_key => $perms_or) {
                $perms = array_merge($perms, explode('||', $perms_or));
            }

            foreach ($perms as $perm) {
                foreach ($role as $role_id => $role) {
                    $field['#name'] = "perm:$perm:$role_id";
                    $field['value'] = 1;
                    $field['checked'] = !empty($access[$perm][$role_id]) && $access[$perm][$role_id] == 1 ? true : false;
                    $form[$field['#name']] = $this->CI->form->form_item_generate($field);
                    $form_values[$field['#name']] = 1;
                }
            }
        }

        if (count($form)) {
            $form['submit'] = array(
                '#name' => 'submit',
                '#type' => 'submit',
                '#item' => array(
                    '#name' => 'submit',
                    'value' => lks_lang('Lưu cấu hình'),
                ),
            );

            $form->submit[] = array(
                'class' => 'role',
                'method' => 'permissions_form_submit',
            );

            $form['#redirect'] = 'role/permissions';
        }

        $form_id = "ZUser-permissions";
        $this->CI->form->form_build($form_id, $form, $form_values, false);
        return $form_id;
    }

    function permissions_form_submit($form_id, $form, &$form_values) {
        $data = [];
        foreach ($form_values as $key => $value) {
            if ($value && substr($key, 0, 5) == 'perm:') {
                $key = explode(':', $key);
                $data[] = array(
                    'id' => $key[2],
                    'name' => $key[1],
                    'access_value' => 1,
                );
            }
        }

        $this->CI->load->model('role_model');
        $this->CI->role_model->access_set_list($data);
        $this->CI->cachef->del_system('user-access_get_list');
        lks_instance_get()->response->addMessage(lks_lang('Dữ liệu của bạn đã được cập nhật thành công.'));
    }


    private function _access_get_key($path) {
        // Support for 3 first element only
        $path = array_slice($path, 0, 3);

        $cache_name ="Roles-_access_get_key-" . serialize($path);
        if ($cache = Cache::get($cache_name)) {
            return $cache;
        }

        $entity = lks_instance_get()->load('\Kalephan\Core\Perms');
        $name = '';
        while (count($path) && !$name) {
            $entity = $this->CI->perms->loadEntity_from_path(implode('/', $path));

            if (isset($entity->name)) {
                $name = $entity->name;
            }
            else {
                array_pop($path);
            }
        }
        $name = $name ? explode("&&", $name) : [];

        if (count($name)) {
            foreach ($name as $key => $val) {
                $name[$key] = explode("||", $val);
            }
        }

        Cache::forever($cache_name, $name);
        return $name;
    }

    function access_get_list($name = '', $cache = true) {
        // Get from cache
        $cache_content = '';
        if ($cache) {
            $cache_content = Cache::get('Roles-access_get_list');
        }

        // Get from database
        if (!$cache_content) {
            $this->CI->load->model('role_model');
            $cache_content = $this->CI->role_model->access_get_list($name);

            // Set to cache
            if ($cache) {
                Cache::forever('Roles-access_get_list', $cache_content);
            }
        }

        if ($name) {
            if (isset($cache_content[$name])) {
                return $cache_content[$name];
            }
        }
        else {
            return $cache_content;
        }

        return [];
    }

    function permissions() {
        $role = $this->role->loadEntityAll(array('cache' => false));
        $access = $this->role->access_get_list('', false);

        $this->load->library('perms');
        $permissions = $this->perms->loadEntityAll(array('cache' => false));

        $vars = array(
            'form_id' => $this->role->permissions_form($role, $permissions, $access),
            'role' => $role,
            'permissions' => $permissions,
            'access' => $access,
        );
        $lks->response->addContent(lks_render('admin_role_permissions', $vars));
    }*/
}