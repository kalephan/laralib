<?php

namespace Kalephan\Metadata;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\Hash;

class MetadataEntity extends EntityAbstract
{

    public function __structure($structure)
    {
        $structure->title = lks_lang('Metadata');
        $structure->indelibility = [
            1
        ];
        $structure->fields = [
            'id' => [
                '#title' => lks_lang('ID')
            ],
            'path' => [
                '#title'    => lks_lang('Đường dẫn'),
                '#type'     => 'text',
                '#validate' => 'required',
                '#required' => true
            ],
            'title' => [
                '#title'    => lks_lang('Tiêu đề'),
                '#type'     => 'text'
            ],
            'active' => [
                '#title'    => lks_lang('Kích hoạt'),
                '#type'     => 'radios',
                '#options'  => [
                    0 => lks_lang('Chưa kích hoạt'),
                    1 => lks_lang('Kích hoạt')
                ],
                '#default'  => 1,
                '#validate' => 'numeric|between:0,1'
            ],
        ];
    }
}