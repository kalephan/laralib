<?php
namespace Kalephan\Mobile\ESMS;

use Kalephan\LKS\EntityAbstract;
use Kalephan\Ec\Shop\ShopEntity;
use Illuminate\Support\Facades\Input;

class ESMSEntity extends EntityAbstract
{

    function __config()
    {
        return array(
            '#id' => 'id',
            '#name' => 'esms',
            '#class' => '\Kalephan\Mobile\ESMS\ESMSEntity',
            '#title' => lks_lang('ESMS Tracking'),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    /*'#title' => lks_lang('ID'),
                    '#type' => 'hidden',*/
                ),
                'smsid' => array(
                    '#name' => 'smsid',
                    /*'#title' => lks_lang('ESMS ID'),
                    '#type' => 'input',*/
                ),
                'data' => array(
                    '#name' => 'content',
                    /*'#title' => lks_lang('Dữ liệu'),
                    '#type' => 'textarea',
                    '#list_hidden' => 1,*/
                ),
                'created_at' => array(
                    '#name' => 'created_at',
                    /*'#title' => lks_lang('Ngày tạo'),
                    '#type' => 'input',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                )
            )
        );
    }

    function showTracking($lks)
    {
        $data = Input::all();
        
        // max 160 characters
        $message = 'Cu phap khong dung. Vui long dang nhap vao chovip.vn de lay thong tin kich hoat.';
        $phone = ! empty($data['sender']) ? $data['sender'] : '';
        if (! empty($data['smsid']) && ! empty($data['sender']) && ! empty($data['content']) && ! empty($data['receivetime']) && ! empty($data['serviceid']) && ! empty($data['sign'])) {
            $sign = base64_encode(md5(getenv('ESMS_CP_ID') . $data['smsid'] . $data['content'] . $data['receivetime'] . getenv('ESMS_AUTH_KEY'), true));
            if (in_array($data['serviceid'], explode('|', getenv('ESMS_SERVICE_ID'))) && $data['sign'] == $sign) {
                $content = substr($data['content'], strrpos($data['content'], ' ') + 1);
                $ec_obj = lks_instance_get()->load('\Kalephan\Ec\Shop\ShopEntity');
                $ec = $ec_obj->loadEntityByPath(strtolower($content));
                
                if (! empty($ec->id)) {
                    $phone = '+' . $phone;
                    if ($ec->mobile == $phone && $ec->active) {
                        $message = 'Tai khoan cua ban tai ChoVip.vn da duoc kich hoat truoc do.';
                    } elseif ($ec->mobile == $phone) {
                        $new_ec = new stdClass();
                        $new_ec->id = $ec->id;
                        $new_ec->active = 2;
                        $ec_obj->entity_save($new_ec);
                        $message = 'Tai khoan cua ban tai ChoVip.vn da duoc kich hoat thanh cong.';
                    } else {
                        $message = 'So dien thoai khong dung. Vui long dang nhap vao chovip.vn de lay thong tin kich hoat.';
                    }
                } else {
                    $message = 'Ten shop khong dung. Vui long dang nhap vao chovip.vn de lay thong tin kich hoat.';
                }
            }
        }
        
        $sms = new stdClass();
        $sms->smsid = ! empty($data['smsid']) ? $data['smsid'] : '';
        $sms->data = serialize($data);
        $id = $this->saveEntity($sms);
        
        echo "<ClientResponse>
<Message>$message</Message>
<Smsid>$id</Smsid >
<Receiver>$phone</Receiver >
</ClientResponse>";
        die();
    }
}