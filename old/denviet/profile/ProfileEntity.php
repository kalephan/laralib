<?php
namespace Kalephan\Profile;

use Kalephan\LKS\EntityAbstract;
use Kalephan\LKS\EntityControllerTrait;
use Kalephan\Core\Form;
use Illuminate\Support\Facades\App;

class ProfileEntity extends EntityAbstract {
    use EntityControllerTrait;

    function __config() {
        return array(
            '#id' => 'id',
            '#name' => 'profiles',
            '#class' => '\Kalephan\Profile\ProfileEntity',
            '#title' => lks_lang('Thông Tin Cá Nhân'),
            '#fields' => array(
                'id' => array(
                    '#name' => 'id',
                    '#title' => lks_lang('ID'),
                    '#type' => 'hidden'
                ),
                'gender' => array(
                    '#name' => 'gender',
                    '#title' => lks_lang('Giới tính'),
                    '#type' => 'select',
                    '#options' => array(
                        '' => lks_lang('Chọn giới tính'),
                        'male' => lks_lang('Nam'),
                        'female' => lks_lang('Nữ'),
                    ),
                    '#required' => true,
                ),
                'birthday' => array(
                    '#name' => 'birthday',
                    '#title' => lks_lang('Ngày sinh'),
                    '#type' => 'date',
                    '#validate' => 'required',
                    '#required' => true,
                    '#config' => array(
                        'form_type' => 'select_group',
                    ),
                    '#attributes' => array(
                        'day' => ['class' => 'date_birth', 'data-required' => ''],
                        'month' => ['class' => 'date_birth', 'data-required' => ''],
                        'year' => ['class' => 'birth', 'data-required' => '', 'data-pattern' => '^\d{4}$', 'placeholder' => 1985],
                    ),
                    '#error_message' => lks_lang('Ngày tháng không hợp lệ'),
                ),
                'address' => array(
                    '#name' => 'address',
                    '#title' => lks_lang('Địa chỉ'),
                    '#type' => 'text',
                    '#attributes' => array(
                        'placeholder' => lks_lang('123 Đại lộ Bình Dương, P.Chánh Nghĩa'),
                        'size' => 100,
                        'data-required' => '',
                    ),
                    '#description' => lks_lang('Không bao gồm tên tỉnh thành và quận huyện trong địa chỉ.'),
                    '#required' => true,
                    '#validate' => 'required||max:100',
                    '#error_message' => lks_lang('Trường này yêu cầu phải nhập.'),
                ),
                'created_by' => array(
                    '#name' => 'created_by',
                    /*'#title' => lks_lang('Tạo bởi'),
                    '#type' => 'text',
                    '#form_hidden' => true,
                    '#list_hidden' => true,*/
                ),
                'updated_by' => array(
                    '#name' => 'updated_by',
                    /*'#title' => lks_lang('Cập nhật bởi'),
                    '#type' => 'text',
                    '#form_hidden' => true,
                    '#list_hidden' => true,*/
                ),
                'created_at' => array(
                    '#name' => 'created_at',
                    /*'#title' => lks_lang('Ngày đăng ký'),
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,*/
                ),
                'updated_at' => array(
                    '#name' => 'updated_at',
                    /*'#title' => lks_lang('Ngày cập nhật'),
                    '#type' => 'text',
                    '#widget' => 'date_timestamp',
                    '#form_hidden' => 1,
                    '#list_hidden' => true,*/
                ),
            ),
        );
    }

    public function showUpdate($lks, $entity_id) {
        // Check User exists
        $user = $lks->load('\Kalephan\User\UserEntity')->loadEntity($entity_id, ['cache' => false]);
        if (!$user) {
            App::abort(403);
        }

        $profile = $this->loadEntity($user->id, ['cache' => false]);
        $form_values = array_merge(lks_object_to_array($profile), lks_object_to_array($user));

        $lks->response->addContent(Form::build($this->structure->class . '@showUpdateForm', $form_values));
    }

    public function showUpdateForm() {
        $form = [];

        $form['email'] = array(
            '#name' => 'email',
            '#type' => 'markup',
            '#title' => lks_lang('Email'),
            '#disabled' => true,
        );

        $user = lks_instance_get()->load('\Kalephan\User\UserEntity');
        $user_structure = $user->getStructure();
        $form['fullname'] = $user_structure->fields['fullname'];

        $form += $this->_showCreateForm();

        $form['id']['#disabled'] = true;

        $form->message = lks_lang('Hồ sơ của bạn đã được cập nhật thành công.');

        array_unshift($form->submit, '\Kalephan\Profile\ProfileEntity@alterShowUpdateFormSubmit');

        return $form;
    }

    public function alterShowUpdateFormSubmit($form_id, &$form, &$form_values) {
        // Update to User
        $user = new \stdClass;
        $user->id = $form_values['id'];
        $user->fullname = $form_values['fullname'];
        $user->updated_at = date('Y-m-d H:i:s');

        $user_obj = lks_instance_get()->load('\Kalephan\User\UserEntity');
        $user_obj->saveEntity($user);
    }
}