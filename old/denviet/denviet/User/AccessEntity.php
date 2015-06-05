<?php
namespace Kalephan\User;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Auth;

class AccessEntity extends EntityAbstract
{

    public function __config()
    {
        return array(
            '#id' => 'name',
            '#name' => 'access',
            '#class' => '\Kalephan\User\AccessEntity',
            '#title' => lks_lang('Quyền truy cập'),
            '#fields' => array(
                'name' => array(
                    '#name' => 'name'
                ),
                'class' => array(
                    '#name' => 'class'
                ),
                'role' => array(
                    '#name' => 'role'
                )
            )
        );
    }

    /*
     * public function showConfig($lks) {
     * $lks->response->addContent(lks_lang('Đang xây dựng'));
     * }
     */
    public function check($name, $user_id = null, $arguments = [])
    {
        $user_id = $user_id ? $user_id : Auth::id();
        $lks = & lks_instance_get();
        
        // true for user 1 (super admin)
        if ($user_id == 1) {
            return true;
        }
        
        if ($user_id) {
            $role = $lks->load('\Kalephan\User\UserEntity');
            $role = $role->loadEntity($user_id);
            $role = array_values($role->role);
        } else {
            $role = [
                1
            ]; // Anonymous role
        }
        
        $name = explode('&', $name);
        foreach ($name as $key => $value) {
            $name[$key] = explode('|', $value);
        }
        
        $result = true;
        // AND
        foreach ($name as $access_and) {
            // OR
            foreach ($access_and as $key) {
                $access = $this->loadEntity($key);
                
                if (! empty($access->role)) {
                    $access->role = explode('|', $access->role);
                    
                    if (count(array_intersect($role, $access->role))) {
                        if (! empty($access->class)) {
                            $segments = explode('@', $access->class);
                            $check = call_user_func_array(array(
                                $lks->load($segments[0]),
                                $segments[1]
                            ), $arguments);
                            if ($check !== true) {
                                $result = $check;
                                continue;
                            }
                        }
                        
                        // Because "OR"
                        $result = true;
                        break;
                    }
                }
                
                $result = false;
            }
            
            // Because "AND"
            if ($result !== true) {
                break;
            }
        }
        
        return $result;
    }
}