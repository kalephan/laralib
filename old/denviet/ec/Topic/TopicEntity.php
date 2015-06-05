<?php
namespace Kalephan\Ec\Topic;

use Kalephan\LKS\EntityAbstract;
use Kalephan\LKS\EntityControllerTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TopicEntity extends EntityAbstract
{
    use EntityControllerTrait;

    function __config()
    {
        return array(
            '#id' => 'id',
            '#name' => 'ec_topics',
            '#class' => '\Kalephan\Ec\Topic\TopicEntity',
            '#title' => lks_lang('Topic'),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => 'ID',
                    '#type' => 'hidden'
                ),
                'title' => array(
                    '#name' => 'title',
                    '#title' => lks_lang('Tiêu đề'),
                    '#type' => 'text',
                    '#validate' => 'required',
                    '#required' => true
                ),
                'short_desc' => array(
                    '#name' => 'short_desc',
                    '#title' => lks_lang('Mô tả ngắn'),
                    '#type' => 'text',
                    '#validate' => 'required',
                    '#required' => true
                ),
                'content' => array(
                    '#name' => 'content',
                    '#title' => lks_lang('Chi tiết'),
                    '#type' => 'textarea',
                    '#rte_enable' => 1
                ),
                'price' => array(
                    '#name' => 'price',
                    '#title' => lks_lang('Giá sản phẩm'),
                    '#type' => 'text',
                    '#validate' => 'required|numeric',
                    '#required' => true
                ),
                'image' => array(
                    '#name' => 'image',
                    '#title' => lks_lang('Ảnh đại diện'),
                    '#type' => 'file',
                    '#widget' => 'image',
                    '#list_hidden' => true,
                    '#validate' => 'required|image|mimes:jpeg,png,gif',
                    '#empty_field_ajax_url' => 'topic/%id/empty-field/image',
                    '#description' => lks_lang('Không chèn quảng cáo, Số điện thoại, Địa chỉ, Tên web... lên ảnh đại diện')
                ),
                'products' => array(
                    '#name' => 'products'
                ),
                'created_by' => array(
                    '#name' => 'created_by',
                    /*'#title' => lks_lang('Tạo bởi'),
                    '#type' => 'text',
                    '#validate' => 'required',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'updated_by' => array(
                    '#name' => 'updated_by',
                    /*'#title' => lks_lang('Cập nhật bởi'),
                    '#type' => 'text',
                    '#validate' => 'required',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'created_at' => array(
                    '#name' => 'created_at',
                    /*'#title' => lks_lang('Taọ lúc'),
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'updated_at' => array(
                    '#name' => 'updated_at  ',
                    /*'#title' => lks_lang('Cập nhật lúc'),
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'deleted_at' => array(
                    '#name' => 'deleted_at  ',
                    /*'#title' => lks_lang('Xóa lúc'),
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'active' => array(
                    '#name' => 'active',
                    /*'#title' => lks_lang('Kích hoạt'),
                    '#type' => 'radios',
                    '#options' => array(
                        1 => lks_lang('Bật'),
                        0 => lks_lang('Tắt')
                    ),
                    '#form_hidden' => 1,
                    '#default' => 0*/
                )
            )
        );
    }
    
    /*
     * function ec_topics_warning($block) {
     * $data = [];
     *
     * return lks_render('ec_topics_block_topic_warning|ec_topics', $data);
     * }
     *
     * function ec_topics_warning_access($block) {
     * $uri = explode('/', \URL::current());
     *
     * if (isset($uri[0]) && $uri[0] == 'e' && isset($uri[1]) && $uri[1] == 'read' && isset($uri[2]) && $uri[2] == 'ec_topics' && isset($uri[3]) && is_numeric($uri[3])) {
     * return true;
     * }
     *
     * return false;
     * }
     *
     * function category_get_posted($id) {
     * $cach_name = "ec_topics-get_category_posted-$id";
     * if ($cache = Cache::get($cach_name)) {
     * return $cache;
     * }
     *
     * $posted = [];
     * $this->CI->load->model('ec_topics_model', '', false, 'ec_topics');
     * $category = $this->CI->ec_topics_model->get_category_posted($id);
     * if (count($category)) {
     * $entity = lks_instance_get()->load('\Kalephan\Category\CategoryEntity');
     * foreach ($category as $value) {
     * $category = $this->CI->category->loadEntity($value->id);
     * if (!empty($category->title)) {
     * $title = $category->title;
     * if (count($category->parent)) {
     * $parent = reset($category->parent)->id;
     * while ($parent) {
     * $category_parent = $this->CI->category->loadEntity($parent);
     * if (!empty($category_parent->title)) {
     * $title = $category_parent->title . ' » ' . $title;
     * $parent = count($category_parent->parent) ? reset($category_parent->parent)->id : 0;
     * }
     * else {
     * $parent = 0;
     * }
     * }
     * }
     *
     * $posted[$value->id] = $title;
     * }
     * }
     * }
     *
     * Cache::forever($cach_name, $posted);
     * return $posted;
     * }
     */
}