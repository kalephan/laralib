<?php
namespace Kalephan\Ec\Cart;

use Kalephan\LKS\EntityAbstract;

class CartEntity extends EntityAbstract {
    /*function __config() {
        return array(
            '#id' => 'id',
            '#name' => 'ec_cart',
            '#class' => '\Kalephan\Ec\Cart\CartEntity',
            '#title' => 'Ec cart',
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => 'ID',
                    '#type' => 'hidden'
                ),
                'products' => array(
                    '#name' => 'products	',
                    '#title' => 'Products',
                    '#type' => 'textarea',
                    '#form_hidden' => 1,
                ),
                'created_by' => array(
                    '#name' => 'created_by	',
                    '#title' => 'tạo b',
                    '#type' => 'textarea',
                    '#form_hidden' => 1,
                ),
                'created_at' => array(
                    '#name' => 'created_at',
                    '#title' => 'created_at',
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,
                ),
                'updated_at' => array(
                    '#name' => 'updated_at	',
                    '#title' => 'updated_at',
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,
                ),
                'active' => array(
                    '#name' => 'active',
                    '#title' => 'Kích hoạt',
                    '#type' => 'radios',
                    '#options' => array(
                        1 => lks_lang('Bật'),
                        0 => lks_lang('Tắt'),
                    ),
                    '#form_hidden' => 1,
                    '#default' => 1,
                ),
            )
        );
    }*/
}