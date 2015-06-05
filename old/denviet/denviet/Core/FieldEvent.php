<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class FieldEvent
{

    private $fields = array(
        'text',
        'textarea',
        'hidden',
        'password',
        'email',
        'file',
        'checkbox',
        'checkboxes',
        'radio',
        'radios',
        'select',
        'submit',
        'button'
    );

    public function fieldValidateFile(&$form_values, $field)
    {
        if (Input::hasFile($field['#name']) && ! Input::file($field['#name'])->isValid()) {
            // @todo 1 add error message
            return false;
        }
    }

    public function createFormSubmitFile(&$form_values, $structure)
    {
        if (Input::hasFile($structure->table)) {
            $file = Input::file($structure->table);
            
            $upload_path = public_path() . config('lks.file path', '/files');
            $upload = false;
            switch ($structure['#widget']) {
                case 'image':
                    $upload_path .= '//f14/images/';
                    $upload = true;
                    break;
            }
            
            if ($upload) {
                $upload_path .= Auth::id();
                $file_name = lks_file_get_filename($file, $upload_path);
                
                if ($file->move($upload_path, $file_name)) {
                    $form_values = str_replace(public_path(), '', $upload_path) . "/$file_name";
                } else {
                    lks_instance_get()->response->addMessage(lks_lang('Một lỗi đã xảy ra. Không thể tải tập tin lên server.'), 'error');
                    $form_values = null;
                }
            }
        }
    }

    public function showReadExecutiveFile(&$value, &$field)
    {
        if (isset($field['#widget']) && $field['#widget'] == 'image' && isset($field['#style'])) {
            $value = lks_style($value, $field['#style']);
        }
    }
}