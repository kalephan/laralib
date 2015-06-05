<?php
namespace Kalephan\Admin;

use Illuminate\Support\Facades\Cache;

class AdminController
{

    function showCacheClear($lks)
    {
        Cache::flush();
        
        $lks->response->addMessage(lks_lang('Cache đã được xóa thành công.'));
        
        return lks_redirect('/');
    }

    public function showDashboard($lks)
    {
        $lks->response->addMessage(lks_lang('Under Construction'));
    }
}