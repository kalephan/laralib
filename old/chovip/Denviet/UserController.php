<?php
namespace Chovip\LKS;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class UserController {
    public function showRegisterSuccess($lks) {
        $user = Session::pull('user registered info', []);

        if (!$user) {
            App::abort(404);
        }

        $lks->response->addContent(lks_render('user-register-success', $user));
    }

    public function showResetPasswordSuccess($lks) {
        $lks->response->addContent(lks_render('user-reset-password-success'));
    }

    public function showUserActivationResendSuccess($lks) {
        $lks->response->addContent(lks_render('user-activation-resend-success'));
    }

    public function showForgotPasswordSuccess($lks) {
        $lks->response->addContent(lks_render('user-forgot-password-success'));
    }
}