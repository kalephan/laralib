<?php
namespace Chovip\Chovip;

use Illuminate\Support\Facades\Session;

class ChovipEvent
{

    public function JSSetting()
    {
        $lks = & lks_instance_get();
        
        $messages = '';
        if ($message = $lks->response->getMessage()) {
            foreach ($message as $key => $value) {
                if (count($value) == 1) {
                    $value = reset($value);
                } else {
                    $value = lks_template_item_list($value);
                }
                
                $messages .= '<div class="message bg-' . $key . '">' . $value . '</div>';
            }
        }
        
        $location = Session::get('location', []);
        
        $data = array(
            'CHOVIP' => array(
                'usermenu' => lks_render('chovip-userpanel', $lks->load('\Chovip\Chovip\Chovip')->getUserPanel()),
                'cart_items' => Session::get('ec_cart_items', 0),
                'messages' => $messages,
                'location_title' => isset($location['title']) ? $location['title'] : lks_lang('Toàn Quốc'),
                'topic_create' => lks_anchor_login('topic/create/start', '<i class="icon_up"></i>ĐĂNG TIN MIỄN PHÍ', [
                    'id' => 'header_newtopic',
                    'class' => 'bg_red no-device'
                ])
            )
        );
        
        $lks->asset->jsAdd($data, 'settings');
    }
}
