<?php
namespace Kalephan\Ec\Shop;

use Kalephan\LKS\EntityAbstract;
use Kalephan\LKS\EntityControllerTrait;
use Kalephan\Core\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShopEntity extends EntityAbstract {
    use EntityControllerTrait;

    public function __config() {
        return array(
            '#id' => 'id',
            '#name' => 'ec_shops',
            '#class' => '\Kalephan\Ec\Shop\ShopEntity',
            '#title' => lks_lang('Shop'),
            '#action_links' => array(
                'list' => '{backend}/shop/list',
                'read' => '{frontend}/shop/%',
                'update' => '{backend}/shop/%/update',
                'delete' => '{backend}/shop/%/delete',
            ),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => lks_lang('ID'),
                    '#type' => 'hidden',
                ),
                'title' => array(
                    '#name' => 'title',
                    '#title' => lks_lang('Tiêu đề shop'),
                    '#type' => 'text',
                    '#required' => true,
                    '#validate' => 'required|min:2|max:100',
                    '#attributes' => array(
                        'placeholder' => lks_lang('Shop Hoa Mai'),
                        'data-required' => '',
                    ),
                    '#description' => lks_lang('Tên shop tối đa 100 ký tự, viết ngắn gọn và không chứa tên miền website'),
                    '#error_message' => lks_lang('Trường này yêu cầu phải nhập.'),
                ),
                'path' => array(
                    '#name' => 'path',
                    '#title' => lks_lang('URL shop'),
                    '#type' => 'text',
                    '#required' => true,
                    '#validate' => 'required|regex:/^[a-z0-9-]{2,32}$/|unique:ec_shops,path|unique:ec_shops_pathpending,path',
                    '#attributes' => array(
                        'placeholder' => lks_lang('shop-hoa-mai'),
                        'size' => '32',
                        'class' => 'urlalias',
                    ),
                    '#ajax' => array(
                        'path' => 'shop/create/check-path',
                        'wrapper' => 'fii_path',
                    ),
                ),
                'active' => array(
                    '#name' => 'active',
                    '#title' => lks_lang('Kích hoạt'),
                    '#type' => 'radios',
                    '#options' => array(
                        1 => lks_lang('Bật'),
                        0 => lks_lang('Tắt'),
                    ),
                    '#default' => 0,
                    '#form_hidden' => 1,
                ),
                'created_by' => array(
                    '#name' => 'created_by',
                    '#title' => lks_lang('Tạo bởi'),
                    /*'#type' => 'text',
                    '#form_hidden' => 1,
                    '#list_hidden' => 1,*/
                ),
                'updated_by' => array(
                    '#name' => 'updated_by',
                    '#title' => lks_lang('Cập nhật bởi'),
                    /*'#type' => 'text',
                    '#form_hidden' => 1,
                    '#list_hidden' => 1,*/
                ),
                'created_at' => array(
                    '#name' => 'created_at',
                    '#title' => lks_lang('Ngày tạo'),
                    /*'#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'updated_at' => array(
                    '#name' => 'updated_at',
                    '#title' => lks_lang('Ngày cập nhật'),
                    /*'#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,
                    '#list_hidden' => 1,*/
                ),
                /*'deleted_at' => array(
                    '#name' => 'deleted_at',
                    '#title' => lks_lang('Ngày xóa'),
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,
                    '#list_hidden' => 1,
                ),*/
            ),
        );
    }

    public function loadEntityByPath($path, $attributes = []) {
        $attributes['where']['path'] = $path;
        return $this->loadEntityWhere($attributes);
    }

    public function loadEntityByUser($id, $attributes = []) {
        $attributes['where']['created_by'] = $id;
        return $this->loadEntityWhere($attributes);
    }

    public function allowCreate() {
        if (!$this->loadEntityByUser(Auth::id())) {
            return true;
        }

        lks_instance_get()->response->addMessage(lks_lang('Mỗi thành viên chỉ được mở một shop.'), 'error');
        return lks_redirect(lks_url('{userpanel}/shop/shop/update'));
    }

    function showCreateCheckPathForm($lks) {
        $form = [];
        $form['path'] = $this->structure->fields['path'];
        $value = $lks->request->query('path', '');

        $validator = Validator::make(
            array('path' => $value),
            array('path' => $form['path']['#validate'])
        );

        $form['path']['#value'] = $value;

        if ($validator->fails()) {
            $error = lks_object_to_array(json_decode($validator->message()));
            $form['path']['#error_message'] = implode('<br />', $error['path']);
            $form['path']['#class'] = isset($form['path']['#class']) ? $form['path']['#class'] . ' error' : 'error';
        }
        else {
            $form['path']['#class'] = isset($form['path']['#class']) ? $form['path']['#class'] . ' success' : 'success';
        }

        $form['path'] = Form::buildItem($form['path']);
        $lks->response->addContent(lks_form_render('path', $form));
    }

    public function convertEntityId($entity_id) {
        $entity_id = parent::convertEntityId($entity_id);

        switch ($entity_id) {
            case 'shop':
                if ($ec = $this->loadEntityByUser(Auth::id())) {
                    $entity_id = $ec->id;
                }
                break;
        }

        return $entity_id;
    }
}