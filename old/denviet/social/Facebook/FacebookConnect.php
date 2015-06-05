<?php
namespace Kalephan\Social\Facebook;

use Kalephan\User\UserEntity;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FacebookConnect
{

    public $appId = '';

    public $appSecret = '';

    public function __construct()
    {
        $this->appId = getenv('FB_APP_ID');
        $this->appSecret = getenv('FB_APP_SECRET');
        
        // @Hack
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getLoginUrl()
    {
        return $this->connect();
    }

    public function connect()
    {
        FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
        $lks = lks_instance_get();
        $query = $lks->request->query('d');
        $query = $query ? "d=$query" : '';
        $helper = new FacebookRedirectLoginHelper(lks_url('{frontend}/social-fb', $query));
        
        try {
            $session = $helper->getSessionFromRedirect();
        } catch (FacebookRequestException $ex) {
            Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        
        // Logged in.
        if ($session) {
            try {
                $social_facebook_user = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject();
                
                $fullname = $social_facebook_user->getProperty('name');
                $email = $social_facebook_user->getProperty('email');
                
                $user_obj = lks_instance_get()->load('\Kalephan\User\UserEntity');
                $user = $user_obj->loadEntityByEmail($email);
                
                // Create a new account
                if (! isset($user->id)) {
                    $user = new \stdClass();
                    $user->email = $email;
                    $user->title = $fullname;
                    $user->active = 1;
                    $user->last_activity = date('Y-m-d H:i:s');
                    $user->password = str_random(16);
                    $user->id = $user_obj->saveEntity($user);
                } else {
                    if ($user->active === 0) {
                        $user->active = 1;
                        $user_obj->saveEntity($user);
                    } elseif ($user->active != 1) {
                        $lks->response->addMessage(lks_lang('Đăng nhập thất bại. Tài khoản của bạn đã bị chặn lại. Vui lòng thử lại sau.'));
                        return lks_redirect(lks_url('{frontend}'));
                    }
                }
                
                Auth::loginUsingId($user->id, true);
                return lks_redirect(lks_url('{frontend}'));
            } catch (FacebookRequestException $ex) {
                Log::error($ex->getMessage());
                $message = lks_lang("Đã có lỗi xãy ra, mã lỗi: :code với thông điệp: :message", array(
                    ':code' => $ex->getCode(),
                    ':message' => $ex->getMessage()
                ));
                $lks->response->addMessage($message, 'error');
                
                return lks_redirect(lks_url('{frontend}'));
            }
        } else {
            return $helper->getLoginUrl() . 'email';
        }
    }
}