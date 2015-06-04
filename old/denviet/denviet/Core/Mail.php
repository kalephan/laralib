<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\Mail as LaravelMail;

class Mail {
    public static function send($job = null, $data) {
        $template = !empty($data['template']) ? $data['template'] : 'email';
        unset($data['template']);

        $data['email'] = explode('|', $data['email']);
        $data['email'][1] = isset($data['email'][1]) ? $data['email'][1] : null;

        $email = array(
            'body' => $data['body'],
        );
        unset($data['body']);

        LaravelMail::send($template, $email, function($message) use ($data) {
            $message->to($data['email'][0], $data['email'][1])
                    ->subject($data['subject']);
        });

        if ($job) {
            $job->delete();
        }
    }
}