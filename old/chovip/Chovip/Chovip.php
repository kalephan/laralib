<?php
namespace Chovip\Chovip;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Chovip
{

    public function getUserPanel()
    {
        $cache_name = __METHOD__ . Auth::id();
        $vars = Cache::get($cache_name, []);
        
        if (! count($vars)) {
            $vars = [];
            $user = lks_user();
            
            if (! empty($user->id)) {
                $user->fullname = $user->fullname ? $user->fullname : $user->username;
                $user->fullname = explode('@', $user->fullname); // get before @ of email
                $tmp = explode(' ', $user->fullname[0]); // get end of full name
                $user->fullname = array_pop($tmp);
                /*
                 * $tmp = array_map(function($str){
                 * return $str[0] . '.';
                 * }, $tmp);
                 * $user->fullname = implode('', $tmp) . ' ' . $user->fullname;
                 *
                 * if (mb_strlen($user->fullname) > 15) {
                 * $user->fullname = trim(trim(mb_substr($user->fullname, 0, 12)), '.') . '...';
                 * }
                 */
                
                $ec_obj = lks_instance_get()->load('\Kalephan\Ec\Shop\ShopEntity');
                $ec_obj = $ec_obj->loadEntityByUser($user->id);
                
                $ec = [];
                if (isset($ec_obj->id)) {
                    if ($ec_obj->active) {
                        $ec['topic/create/start'] = lks_lang('Đăng sản phẩm');
                        $ec['topic/shop/list'] = lks_lang('Quản lý topic');
                        
                        if ($ec_obj->active == 1) {
                            $ec['shop-order/shop/list'] = lks_lang('Quản lý đơn hàng');
                            // $ec['comment/shop/list'] = lks_lang('Quản lý bình luận');
                            // $ec['customer/shop/list'] = lks_lang('Quản lý khách hàng');
                        }
                    } else {
                        $ec['shop/shop/confirmation'] = lks_lang('Kích hoạt shop');
                    }
                    $ec['shop/shop/update'] = lks_lang('Thông tin shop');
                }
                
                $vars = array(
                    'username' => $user->fullname,
                    'shop' => $ec
                );
            }
            
            lks_cache_set($cache_name, $vars);
        }
        
        return $vars;
    }
}