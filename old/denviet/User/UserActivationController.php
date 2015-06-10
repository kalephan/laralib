<?php
namespace Kalephan\User;

use Kalephan\Core\Form;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class UserActivationController
{

    private $user;

    private $onetimelink;

    public function __construct()
    {
        $lks = & lks_instance_get();
        $this->user = $lks->load('\Kalephan\User\UserEntity');
        $this->onetimelink = $lks->load('\Kalephan\OTL\OTLEntity');
    }

    public function showUserActivation($lks, $hash)
    {
        $hash = $this->onetimelink->loadEntityByHash($hash, 'user-activation');
        
        if (! $hash) {
            App::abort(403);
        }
        
        $activation = false;
        if (isset($hash->destination)) {
            // Active
            $user = $this->user->loadEntity($hash->destination);
            if ($user) {
                $user->active = 1;
                $this->user->saveEntity($user);
                
                // Login
                Auth::loginUsingId($user->id);
                
                $lks->response->addMessage(lks_lang('Tài khoản của bạn đã được kích hoạt thành công.'));
                $activation = true;
            }
        }
        
        if (! $activation) {
            $lks->response->addMessage(lks_lang('Đường dẫn kích hoạt tài khoản đã hết hạn. Vui lòng sử dụng tính năng gửi lại mã kích hoạt.'), 'error');
        }
        
        return lks_redirect(lks_url('{frontend}'));
    }

    public function showUserActivationResend($lks)
    {
        $lks->response->addContent(Form::build('\Kalephan\User\UserActivationController@showUserActivationResendForm'));
    }

    public function showUserActivationResendForm()
    {
        $structure = $this->user->getStructure();
        $form = [];
        
        $form['email'] = $structure->fields['email'];
        $form['email']['#validate'] .= '|exists:users,email';
        
        $form->actions['submit'] = array(
            '#name' => 'submit',
            '#type' => 'submit',
            '#value' => lks_lang('Kích hoạt tài khoản')
        );
        
        $form->submit[] = 'Kalephan\User\UserActivationController@showUserActivationResendFormSubmit';
        $form['#redirect'] = '/';
        $form->message = lks_lang('Mã kích hoạt đã được gửi tới email của bạn.');
        
        return $form;
    }

    public function showUserActivationResendFormSubmit($form_id, $form, &$form_values)
    {
        $user = $this->user->loadEntityByEmail($form_values['email']);
        $hash = $this->onetimelink->setHash($user->id, 'user-activation');
        
        $vars = array(
            'email' => $form_values['email'],
            'link' => lks_url("{userpanel}/user/activation/" . $hash->hash),
            'expired' => $hash->expired
        );
        
        lks_mail($form_values['email'], lks_lang(config('lks.create user activation resend email subject', 'Gửi lại mã kích hoạt tài khoản của bạn')), lks_render('user-create-activation-resend-email', $vars));
    }
}