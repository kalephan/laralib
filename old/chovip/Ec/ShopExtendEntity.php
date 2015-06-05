<?php
namespace Chovip\Ec;

use Kalephan\LKS\EntityAbstract;
use Kalephan\Core\Form;

class ShopExtendEntity extends EntityAbstract
{

    public function __config()
    {
        return array(
            '#id' => 'id',
            '#name' => 'ec_shops_extend',
            '#class' => '\Chovip\Ec\ShopExtendEntity',
            '#title' => lks_lang('Ec Extend'),
            '#approve' => true,
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => lks_lang('ID'),
                    '#type' => 'hidden'
                ),
                'shop_paymenth' => array(
                    '#name' => 'shop_paymenth',
                    '#title' => lks_lang('Thông tin thanh toán'),
                    '#type' => 'textarea',
                    '#rte_enable' => 1,
                    '#list_hidden' => 1,
                    '#description' => lks_lang('Nội dung ở đây sẽ được hiển thị trong phần "Phương thức thanh toán" khi người dùng xem một sản phẩm của ec.')
                ),
                'shop_shipmenth' => array(
                    '#name' => 'shop_shipmenth',
                    '#title' => lks_lang('Thông tin giao hàng'),
                    '#type' => 'textarea',
                    '#rte_enable' => 1,
                    '#list_hidden' => 1,
                    '#description' => lks_lang('Nội dung ở đây sẽ được hiển thị trong phần "Phương thức giao hàng" khi người dùng xem một sản phẩm của ec.')
                ),
                'shop_aboutus' => array(
                    '#name' => 'shop_aboutus',
                    '#title' => lks_lang('Giới thiệu'),
                    '#type' => 'textarea',
                    '#rte_enable' => 1,
                    '#list_hidden' => 1,
                    '#description' => lks_lang('Nội dung ở đây sẽ được hiển thị trong trang "Giới thiệu" khi người dùng viếng thăm trang chủ của ec.')
                ),
                'shop_contact' => array(
                    '#name' => 'shop_contact',
                    '#title' => lks_lang('Liên hệ'),
                    '#type' => 'textarea',
                    '#rte_enable' => 1,
                    '#list_hidden' => 1,
                    '#description' => lks_lang('Nội dung ở đây sẽ được hiển thị trước biểu mẫu gửi tin nhắn của trang "Liên hệ" khi người dùng viếng thăm trang chủ của ec.')
                ),
                'approve' => array(
                    '#name' => 'approve',
                    '#title' => lks_lang('Mã phê duyệt'),
                    '#type' => 'hidden',
                    '#list_hidden' => 1,
                    '#form_hidden' => 1,
                    '#display_hidden' => 1
                )
            )
        );
    }
}