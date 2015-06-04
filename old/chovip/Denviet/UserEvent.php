<?php
namespace Chovip\LKS;

use Illuminate\Support\Facades\Session;

class UserEvent {
    public function structureAlterUser(&$structure) {
        $structure->fields['fullname']['#attributes']['placeholder'] = lks_lang('Nguyễn Văn A');
        $structure->fields['fullname']['#description'] = lks_lang('Họ tên tối đa 100 ký tự, và tối thiểu phải là 2 từ.');
        $structure->fields['email']['#attributes']['placeholder'] = lks_lang('nguyenvana@gmail.com');
        $structure->fields['email']['#description'] = lks_lang('Vui lòng nhập email thực sự của bạn. Chúng tôi sẽ gửi cho bạn một email để kích hoạt tài khoản của bạn.');
        $structure->fields['password']['#validate'] = 'min:8|max:36';
        $structure->fields['password']['#attributes']['data-validate'] = 'password';
        $structure->fields['password']['#description'] = lks_lang('Phải có ít nhất 8 ký tự');
        $structure->fields['password']['#error_message'] = lks_lang('Mật khẩu phải có ít nhất 8 ký tự');
    }

    public function formAlterRegister($form_id, &$form) {
        unset($form['username']);

        $form['accepted'] = array(
            '#name' => 'accepted',
            '#type' => 'checkbox',
            '#value' => 1,
            '#title' => lks_lang('Tôi đồng ý với :term và :policy', array(
                ':term' => lks_anchor('article/3', lks_lang('Thỏa Thuận Sử Dụng'), array('target' => '_blank')),
                ':policy' => lks_anchor('article/4', lks_lang('Chính Sách Bảo Mật'), array('target' => '_blank')),
            )),
            '#validate' => 'accepted',
            '#attributes' => array(
                'data-required' => '',
            ),
            '#error_message' => lks_lang('Bạn phải đọc và chấp nhận các chính sách của chúng tôi mới có thể đăng ký thành viên.'),
        );

        $form->actions['reset'] = array(
            '#name' => 'reset',
            '#type' => 'reset',
            '#value' => lks_lang('Nhập lại'),
        );

        $form->submit[] = '\Chovip\Kalephan\UserEvent@formAlterRegisterSubmit';
        $form['#redirect'] = lks_url('{userpanel}/user/register/success');
        $form->message = '';
    }

    public function formAlterRegisterSubmit($form_id, &$form, &$form_values) {
        $data = array(
            'fullname' => $form_values['fullname'],
            'email' => $form_values['email'],
        );
        Session::set('user registered info', $data);
    }

    public function formAlterLogin($form_id, &$form) {
        unset($form['email']['#description']);

        $form['#theme'] = 'user-login';
    }

    public function formAlterUserActivationResend($form_id, &$form) {
        $form['#redirect'] = lks_url('{userpanel}/user/activation/resend/success');
        $form->message = '';
    }

    public function formAlterForgotPassword($form_id, &$form) {
        $form['#redirect'] = lks_url('{userpanel}/user/forgotpass/success');
        $form->message = '';
    }

    public function formAlterResetPassword($form_id, &$form) {
        $form['#redirect'] = lks_url('{userpanel}/user/resetpass/success');
        $form->message = '';
    }
}