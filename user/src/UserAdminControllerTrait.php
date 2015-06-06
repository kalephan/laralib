<?php

namespace Kalephan\User;

use Kalephan\LKS\Facades\Form;
use Kalephan\LKS\Facades\Output;
use Illuminate\Pagination\Paginator;

trait UserAdminControllerTrait
{

    public function getIndex()
    {
        Output::titleAdd(lks_lang('Danh sách thành viên'));
        
        $user = new UserEntity();
        
        $content = [];
        if ($users = $user->loadEntityPaginate(2)) {
            $content['table'] = lks_entities2table($users['entities'], $user->structure()->fields);
            
            $content['paginator'] = $users['paginator']->render();
        }
        
        return lks_view('page-user-list', $content);
    }

    public function getAdd()
    {
        Output::titleAdd(lks_lang('Thêm thành viên'));
        
        return lks_view('page', [
            'content' => Form::build('Kalephan\User\UserForm@formAdd')
        ]);
    }

    public function postAdd()
    {
        return Form::submit();
    }
}