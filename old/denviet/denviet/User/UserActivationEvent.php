<?php
namespace Kalephan\User;

class UserActivationEvent
{

    public function sendUserActivationEmail($entity)
    {
        if ($entity->active == 0 && config('lks.create user need activation', 1)) {
            $onetimelink = lks_instance_get()->load('\Kalephan\OTL\OTLEntity');
            $hash = $onetimelink->setHash($entity->id, 'user-activation');
            
            $vars = array(
                'email' => $entity->email,
                'link' => lks_url("{userpanel}/user/activation/" . $hash->hash),
                'expired' => $hash->expired
            );
            
            lks_mail($entity->email, lks_lang(config('lks.create user need activation subject', 'Kích hoạt tài khoản của bạn')), lks_render('user-create-activation-email', $vars), 'email');
        }
    }
}