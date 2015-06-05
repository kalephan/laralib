<?php
namespace Kalephan\Article;

use Kalephan\LKS\EntityAbstract;
use Kalephan\LKS\EntityControllerTrait;

class ArticleEntity extends EntityAbstract
{
    use EntityControllerTrait;

    function __config()
    {
        return array(
            '#id' => 'id',
            '#name' => 'articles',
            '#class' => '\Kalephan\Article\ArticleEntity',
            '#title' => lks_lang('Article'),
            '#action_links' => array(
                'list' => '{backend}/article/list',
                'create' => '{backend}/article/create',
                'clone' => '{backend}/article/%/clone',
                'read' => '{frontend}/article/%',
                'update' => '{backend}/article/%/update',
                'delete' => '{backend}/article/%/delete'
            ),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => lks_lang('ID'),
                    '#type' => 'hidden'
                ),
                'title' => array(
                    '#name' => 'title',
                    '#title' => lks_lang('Tiêu đề'),
                    '#type' => 'text'
                ),
                'image' => array(
                    '#name' => 'image',
                    '#title' => lks_lang('Hình ảnh'),
                    '#type' => 'file',
                    '#widget' => 'image',
                    '#style' => 'normal',
                    '#list_hidden' => true,
                    '#validate' => 'image|mimes:jpeg,png,gif',
                    '#empty_field_ajax_url' => 'article/%id/empty-field/image'
                ),
                'summary' => array(
                    '#name' => 'summary',
                    '#title' => lks_lang('Tóm tắt'),
                    '#type' => 'textarea',
                    '#list_hidden' => true
                ),
                'content' => array(
                    '#name' => 'content',
                    '#title' => lks_lang('Nội dung'),
                    '#type' => 'textarea',
                    '#rte_enable' => true,
                    '#list_hidden' => true
                ),
                'active' => array(
                    '#name' => 'active',
                    '#title' => lks_lang('Kích hoạt'),
                    '#type' => 'radios',
                    '#options' => array(
                        1 => lks_lang('Bật'),
                        0 => lks_lang('Tắt')
                    ),
                    '#validate' => 'required|numeric',
                    '#default' => 1
                ),
                'created_at' => array(
                    '#name' => 'created_at',
                    '#title' => lks_lang('Ngày tạo'),
                    /*'#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => true,*/
                ),
                'updated_at' => array(
                    '#name' => 'updated_at',
                    '#title' => lks_lang('Ngày cập nhật'),
                    /*'#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => true,*/
                ),
                'created_by' => array(
                    '#name' => 'created_by',
                    '#title' => lks_lang('Tạo bởi'),
                    /*'#type' => 'text',
                    '#form_hidden' => true,
                    '#list_hidden' => true,*/
                ),
                'updated_by' => array(
                    '#name' => 'updated_by',
                    '#title' => lks_lang('Cập nhật bởi'),
                    /*'#type' => 'text',
                    '#form_hidden' => true,
                    '#list_hidden' => true,*/
                )
            )
        );
    }
}