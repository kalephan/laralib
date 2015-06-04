<?php
namespace Kalephan\User;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserEntity extends EntityAbstract {

    public function login($email, $password, $remember = false) {
        $logined = Auth::attempt(array(
                'email' => $email,
                'password' => $password,
                'active' => 1,
            ), $remember);

        if ($logined) {
            // Update last_activity field
            $user = lks_user();
            $user->last_activity = date('Y-m-d H:i:s');

            $data = ['user' => &$user];
            event('user.login', $data);
            $user = $data['user'];

            $this->saveEntity($user);
            return true;
        }

        return false;
    }

    public function logout() {
        Auth::logout();

        event('user.logout');
    }
}
