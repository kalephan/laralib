<?php
namespace Kalephan\Ec\Product;

use Kalephan\LKS\EntityAbstract;

class ProductEntity extends EntityAbstract {

    function __config() {
        return array(
            '#id' => 'id',
            '#name' => 'ec_products',
            '#class' => '\Kalephan\Ec\Product\ProductEntity',
            '#title' => 'Sản phẩm',
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => 'ID',
                    '#type' => 'hidden'
                ),
                'title' => array(
                    '#name' => 'title',
                    '#title' => lks_lang('Tên sản phẩm'),
                    '#type' => 'text',
                    '#validate' => 'required',
                    '#attributes' => array(
                        'data-required' => '',
                    ),
                ),
                'shor_desc' => array(
                    '#name' => 'shor_desc',
                    '#title' => lks_lang('Mô tả ngắn'),
                    '#type' => 'textarea',
                ),
                'price' => array(
                    '#name' => 'price',
                    '#title' => lks_lang('Giá'),
                    '#type' => 'text',
                    '#validate' => 'required|numeric',
                    '#attributes' => array(
                        'data-required' => '',
                        'data-validate' => 'price',
                    ),
                ),
                'image' => array(
                    '#name' => 'image',
                    '#title' => lks_lang('Hình ảnh'),
                    '#type' => 'file',
                    '#widget' => 'image',
                    '#list_hidden' => true,
                    '#validate' => 'image|mimes:jpeg,png,gif',
                    '#empty_field_ajax_url' => 'product/%id/empty-field/image',
                ),
                'created_by' => array(
                    '#name' => 'created_by',
                    '#title' => lks_lang('Tạo bởi'),
                    /*'#type' => 'text',
                    '#validate' => 'required',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'updated_by' => array(
                    '#name' => 'updated_by',
                    '#title' => lks_lang('Cập nhật bởi'),
                    /*'#type' => 'text',
                    '#validate' => 'required',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'created_at' => array(
                    '#name' => 'created_at',
                    '#title' => lks_lang('Taọ lúc'),
                    /*'#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'updated_at' => array(
                    '#name' => 'updated_at	',
                    '#title' => lks_lang('Cập nhật lúc'),
                    /*'#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                /*'deleted_at' => array(
                    '#name' => 'deleted_at  ',
                    '#title' => lks_lang('Xóa lúc'),
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,
                ),*/
                'active' => array(
                    '#name' => 'active',
                    '#title' => lks_lang('Kích hoạt'),
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
    }
}