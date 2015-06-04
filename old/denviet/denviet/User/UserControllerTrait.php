<?php
namespace Kalephan\User;

use Kalephan\Core\Form;
use Kalephan\LKS\EntityControllerTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait UserControllerTrait {
    use EntityControllerTrait {
        showCreateForm as showCreateFormTrait;
    }

    private $user;

    public function showRegisterFull($lks) {
        $lks->response->addContent(Form::build('\Kalephan\User\UserEntity@showCreateForm'));
    }

    public function showRegister($lks) {
        $lks->response->addContent(Form::build('\Kalephan\User\UserEntity@showRegisterForm'));
    }

    public function showCreateForm(){
        $form = $this->showCreateFormTrait();

        $temp_active = $form['active'];
        unset($form['active']);
        $temp_role = $form['role'];
        unset($form['role']);

        $form = array_merge($form, $this->_passwordFields());

        // Validate email unique
        $form['email']['#validate'] .= '|unique:users,email';

        $form->actions['submit']['#value'] = lks_lang('Đăng ký');

        $form['active'] = $temp_active;
        $form['role'] = $temp_role;

        return $form;
    }

    function showRegisterForm() {
        $form = $this->showCreateForm();
        unset($form['active'], $form['role']);

        return $form;
    }

    public function showLogin($lks) {
        $lks->response->addContent(Form::build('\Kalephan\User\UserEntity@showLoginForm'));
    }

    function showLoginForm() {
        $form = [];
        $structure = $this->getStructure();

        $form['email'] = $structure->fields['email'];
        $form['email']['#validate'] .= '|exists:users,email';

        $form['password'] = $structure->fields['password'];

        $form->actions['submit'] = array(
            '#name' => 'submit',
            '#type' => 'submit',
            '#value' => lks_lang('Đăng nhập'),
        );

        $form['remember_me'] = array(
            '#name' => 'remember_me',
            '#type' => 'checkbox',
            '#value' => 1,
            '#title' => lks_lang('Ghi nhớ đăng nhập'),
        );

        $form->validate[] = '\Kalephan\User\UserEntity@showLoginFormValidate';
        $form['#redirect'] = '/';
        $form->message = lks_lang('Bạn đã đăng nhập thành công vào hệ thống...');

        return $form;
    }

    function showLoginFormValidate($form_id, &$form, &$form_values) {

        $form_values['remember_me'] = !empty($form_values['remember_me']) ? true : false;

        // Login successfull
        if ($this->login($form_values['email'], $form_values['password'], $form_values['remember_me'])) {
            return true;
        }

        // ----------- Login fail -----------

        $user = $this->loadEntityByEmail($form_values['email']);

        $error_msg = lks_lang('Đăng nhập thất bại: ');
        if (!isset($user->email)) {
            $form->error['email'][] = $error_msg . lks_lang('Tài khoản !email không tồn tại.', ['!email' => $form_values['email']]);
        }
        elseif ($user->active == 0) {
            $form->error['#form'] = $error_msg . lks_lang('Tài khoản của bạn chưa được kích hoạt.');
        }
        elseif ($user->active != 1) {
            $form->error['#form'] = $error_msg . lks_lang('Tài khoản của bạn đã bị chặn bởi người quản trị.');
        }
        else {
            $form->error['password'][] = $error_msg . lks_lang('Mật khẩu của bạn không chính xác.');
        }

        return false;
    }

    public function showLogout($lks) {
        $this->logout();
        $lks->response->addMessage(lks_lang('Bạn đã đăng xuất thành công.'));

        return lks_redirect(lks_url('{frontend}'));
    }

    public function showChangePassword($lks) {
        $lks->response->addContent(Form::build('\Kalephan\User\UserEntity@showChangePasswordForm'));
    }

    function showChangePasswordForm() {
        $structure = $this->getStructure();

        $form = [];
        $form['password_old'] = $structure->fields['password'];
        $form['password_old']['#name'] = 'password_old';
        $form['password_old']['#title'] = lks_lang('Mật khẩu cũ');

        $form = array_merge($form, $this->_passwordFields());

        $form['password']['#title'] = lks_lang('Mật khẩu mới');
        $form['password_confirm']['#error_message'] = lks_lang('Xác nhận mật khẩu mới không trùng khớp với mật khẩu mới');

        $form->actions['submit'] = array(
            '#name' => 'submit',
            '#type' => 'submit',
            '#value' => lks_lang('Thay đổi mật khẩu'),
        );

        $form->validate[] = '\Kalephan\User\UserEntity@showChangePasswordFormValidate';
        $form->submit[] = '\Kalephan\User\UserEntity@showChangePasswordFormSubmit';
        $form->message = lks_lang('Mật khẩu của bạn đã được thay đổi thành công.');

        return $form;
    }

    function showChangePasswordFormValidate($form_id, &$form, &$form_values) {
        if (Hash::check($form_values['password_old'], Auth::user()->__get('password'))) {
            return true;
        }

        $form->error['password_old'][] = lks_lang('Mật khẩu cũ của bạn không phù hợp.');

        return false;
    }

    function showChangePasswordFormSubmit($form_id, &$form, &$form_values){
        $user = $this->loadEntity(Auth::id());
        $user->password = $form_values['password'];
        $this->saveEntity($user);
    }

    public function showForgotPassword($lks) {
        $lks->response->addContent(Form::build('\Kalephan\User\UserEntity@showForgotPasswordForm'));
    }

    function showForgotPasswordForm() {
        $structure = $this->getStructure();
        $form = [];

        $form['email'] = $structure->fields['email'];
        $form['email']['#validate'] .= '|exists:users,email';


        $form->actions['submit'] = array(
            '#name' => 'submit',
            '#type' => 'submit',
            '#value' => lks_lang('Quên mật khẩu'),
        );

        $form->submit[] = 'Kalephan\User\UserEntity@showForgotPasswordFormSubmit';
        $form->message = lks_lang('Chúng tôi đã gửi email cho bạn. Kiểm tra email và làm theo hướng dẫn để lấy lại mật khẩu.');

        return $form;
    }

    function showForgotPasswordFormSubmit($form_id, $form, &$form_values) {
        $user = $this->loadEntityByEmail($form_values['email']);

        $onetimelink = lks_instance_get()->load('\Kalephan\OTL\OTLEntity');;
        $hash = $onetimelink->setHash($user->id, 'user-forgotpass');

        $vars = array(
            'title' => $user->fullname,
            'link' => lks_url("{userpanel}/user/resetpass/" . $hash->hash),
        );

        lks_mail($form_values['email'],
            lks_lang(config('lks.user forgotpass email subject', 'Thiết lập lại mật khẩu của bạn')),
            lks_render('user-reset-password-email', $vars)
        );
    }

    function showResetPassword($lks, $hash) {
        $onetimelink = $lks->load('\Kalephan\OTL\OTLEntity');
        $hash = $onetimelink->loadEntityByHash($form_values['hash'], 'user-forgotpass');

        if (isset($hash->destination)) {
            Auth::loginUsingId($hash->destination);

            $lks->response->addContent(Form::build("\Kalephan\User\UserEntity@showResetPasswordForm@"));
        }
        else {
            $lks->response->addMessage(lks_lang('Liên kết đổi mật khẩu không đúng hoặc đã hết hạn sử dụng. Vui lòng lấy thực hiện lại quy trình lấy mật khẩu mới.'), 'error');
            return lks_redirect(lks_url('{frontend}/user/forgotpass'));
        }
    }

    function showResetPasswordForm() {
        $form = $this->_passwordFields();

        $form->actions['submit'] = array(
            '#name' => 'submit',
            '#type' => 'submit',
            '#value' => lks_lang('Thiết lập lại mật khẩu'),
        );

        $form->submit[] = 'Kalephan\User\UserEntity@showChangePasswordFormSubmit';
        $form->message = lks_lang('Mật khẩu của bạn đã được thay đổi thành công.');

        return $form;
    }

    private function _passwordFields() {
        $structure = $this->getStructure();

        $form = [];

        $form['password'] = $structure->fields['password'];

        $form['password_confirm'] = $structure->fields['password'];
        $form['password_confirm']['#name'] = 'password_confirm';
        $form['password_confirm']['#title'] = lks_lang('Xác nhận mật khẩu');
        $form['password_confirm']['#attributes']['data-validate'] = 'password_confirm';
        $form['password_confirm']['#error_message'] = lks_lang('Xác nhận mật khẩu không trùng khớp với mật khẩu');

        return $form;
    }

    /*
    function showUpdate($lks, $userid) {
        $form_values = $this->loadEntity($userid);

        // Clear cache user panel
        lks_config_set('user panel update', 1);

        $form = array(
            'class' => '\Kalephan\User\UserEntity',
            'method' => 'showUpdateForm',
        );
        $lks->response->addContent(Form::build($form, $form_values));
    }

    function showUpdateForm() {
        $form = $this->showCreateForm();

        $form['email']['#disabled'] = true;
        $form['email']['#attributes']['disabled'] = 'disabled';
        unset($form['email']['#description']);

        $form['username']['#disabled'] = true;
        $form['username']['#attributes']['disabled'] = 'disabled';

        unset($form['password']);
        unset($form['password_confirm']);

        return $form;
    }*/
}