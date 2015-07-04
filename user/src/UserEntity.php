<?php

namespace Kalephan\User;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Hash;

class UserEntity extends EntityAbstract
{

    public function __structure($structure)
    {
        $structure->title = lks_lang('Thành viên');
        $structure->indelibility = [
            1
        ];
        $structure->url_prefix = 'user';
        $structure->fields = [
            'id' => [
                '#title' => lks_lang('ID')
            ],
            'name' => [
                '#not_listed' => true
            ],
            'email' => [
                '#title'    => lks_lang('Email'),
                '#type'     => 'text',
                '#validate' => 'required|email',
                '#required' => true
            ],
            'password' => [
                '#not_listed' => true,
                '#title'      => lks_lang('Mật khẩu')
            ],
            'active' => [
                '#title'    => lks_lang('Kích hoạt'),
                '#type'     => 'radios',
                '#options'  => [
                    0 => lks_lang('Chưa kích hoạt'),
                    1 => lks_lang('Kích hoạt'),
                    2 => lks_lang('Bị chặn')
                ],
                '#default'  => 0,
                '#validate' => 'numeric|between:0,2'
            ],
            'remember_token' => [
                '#not_listed' => true
            ],
            'last_activity' => [
                '#not_listed' => true
            ],
            'created_at' => [
                '#title' => lks_lang('Ngày tạo')
            ],
            'updated_at' => [
                '#title' => lks_lang('Ngày cập nhật')
            ]
        ];
    }

    public function saveEntity($entity, $active_action = false)
    {
        if (! empty($entity->password)) {
            $entity->password = Hash::make($entity->password);
        }

        // Only use for create a new user case
        if (empty($entity->id) && empty($entity->name)) {
            $entity->name = $entity->email;
        }

        return parent::saveEntity($entity);
    }

    public function loadEntity($entity_id, $check_active = false)
    {
        $entity = parent::loadEntity($entity_id, $check_active);

        if (isset($entity->password)) {
            unset($entity->password);
        }

        return $entity;
    }

    public function loadEntityByEmail($email)
    {
        if (! lks_validate_email($email)) {
            return null;
        }

        return $this->loadEntityWhere([
            [
                'email',
                '=',
                $email
            ]
        ]);
    }
}