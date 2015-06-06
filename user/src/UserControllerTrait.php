<?php

namespace Kalephan\User;

use Kalephan\LKS\Facades\Form;
use Kalephan\LKS\Facades\Output;
use Illuminate\Pagination\Paginator;

trait UserControllerTrait
{
    public function getLogin()
    {
        Output::titleAdd(lks_lang('Đăng nhập'));
        
        return lks_view('page', [
            'content' => Form::build('Kalephan\User\UserForm@formCreate')
        ]);
    }

    public function postLogin()
    {
        return Form::submit();
    }
    
    public function getRegister()
    {
        Output::titleAdd(lks_lang('Đăng ký thành viên'));
        
        return lks_view('page', [
            'content' => Form::build('Kalephan\User\UserForm@formCreate')
        ]);
    }

    public function postRegister()
    {
        return Form::submit();
    }
    
    public function getForgotpass()
    {
        Output::titleAdd(lks_lang('Quên mật khẩu'));
        
        return lks_view('page', [
            'content' => Form::build('Kalephan\User\UserForm@formCreate')
        ]);
    }

    public function postForgotpass()
    {
        return Form::submit();
    }
    
    public function getLogout()
    {
    }
    
    public function getChangepass()
    {
        Output::titleAdd(lks_lang('Đổi mật khẩu'));
        
        return lks_view('page', [
            'content' => Form::build('Kalephan\User\UserForm@formCreate')
        ]);
    }

    public function postChangepass()
    {
        return Form::submit();
    }
}