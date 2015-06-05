<?php

namespace Kalephan\LKS;

use Kalephan\LKS\Facades\Asset;
use Kalephan\LKS\Facades\Output;
use Illuminate\Html\FormFacade as LaravelForm;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class Form
{

    public function build($class, $form_values = [])
    {
        $cache_name_prefix = lks_cache_name(__CLASS__ . '-build');
        
        $class = explode('@', $class);
        $form_id = lks_str_slug($class[0] . '-' . $class[1]);
        $form_token = Input::get('_form_token', md5(time() . csrf_token() . Request::path()));
        
        // Rebuild from error form
        $cache_name_error_form = "$cache_name_prefix-$form_id-$form_token";
        if ($cache_value = Cache::get($cache_name_error_form)) {
            Cache::forget($cache_name_error_form);
            $form = $cache_value;
        } else {
            $cache_name = "$cache_name_prefix-$form_id";
            if ($cache_value = Cache::get($cache_name)) {
                $form = $cache_value;
            } else {
                $form = self::formInit($form_id);
                call_user_func([
                    new $class[0](),
                    $class[1]
                ], $form);
                event('lks.formAlter', $form);
                event("lks.formAlter: $form_id", $form);
                self::buildBeforeCache($form);
                Cache::forever($cache_name, $form);
            }
            
            if ($form_values) {
                $form->values = lks_object_to_array($form_values);
            }
            
            event("lks.formValueAlter", $form);
            event("lks.formValueAlter: $form_id", $form);
            
            // Set default value for form
            self::setValues($form);
        }
        
        $form->fields['_form_token']['#value'] = $form_token;
        self::buildAfterCache($form);
        
        // Create cache to use when form submitted
        lks_cache_set($cache_name_error_form, $form, config('session.lifetime', 120));
        
        return view($form->theme, [
            'form' => $form
        ]);
    }

    private static function formInit($form_id)
    {
        $form = new \stdClass();
        
        $form->connection = '';
        $form->error = [];
        $form->form = [];
        $form->id = $form_id;
        $form->message = [];
        $form->redirect = '';
        $form->submit = [];
        $form->theme = 'form';
        $form->validate = [];
        $form->values = [];
        $form->variable = [];
        
        $form->actions = [];
        $form->actions['submit'] = [
            '#name' => 'submit',
            '#type' => 'submit',
            '#value' => lks_lang('Submit')
        ];
        
        $form->fields = [];
        $form->fields['_form_id'] = [
            '#name' => '_form_id',
            '#type' => 'hidden',
            '#value' => $form_id,
            '#disabled' => true
        ];
        $form->fields['_form_token'] = [
            '#name' => '_form_token',
            '#type' => 'hidden',
            '#value' => '',
            '#disabled' => true
        ];
        
        return $form;
    }

    private static function setValues($form)
    {
        foreach ($form->fields as $key => $value) {
            if (isset($form->values[$key])) {
                $form->fields[$key]['#value'] = $value;
            }
        }
        
        /*
         * foreach ($form->fields as $key => $value) {
         * switch ($key) {
         * case 'group':
         * foreach ($value as $k => $v) {
         * if (isset($form[$v]) && count($form[$v])) {
         * self::setValues($form_id, $form[$v], $form_values);
         * }
         * }
         * break;
         *
         * default:
         * if (isset($form_values[$key])) {
         * if (isset($value['#type'])) {
         * $empty_field_ajax_url = '';
         * if(!empty($value['#empty_field_ajax_url'])) {
         * if (strpos($value['#empty_field_ajax_url'], '%') !== false) {
         * $value['#empty_field_ajax_url'] = explode('/', $value['#empty_field_ajax_url']);
         * foreach ($value['#empty_field_ajax_url'] as $k => $v) {
         * if (strpos($v, '%') !== false && isset($form_values[substr($v, 1)])) {
         * $value['#empty_field_ajax_url'][$k] = $form_values[substr($v, 1)];
         * }
         * }
         * $value['#empty_field_ajax_url'] = implode('/', $value['#empty_field_ajax_url']);
         * }
         *
         * $empty_field_ajax_url = '<div class="empty-field ajax-run btn btn-link" data-url="'.$value['#empty_field_ajax_url'].'">['.lks_lang('Hủy bỏ').']</div>';
         * }
         *
         * switch ($value['#type']) {
         * // Do not tracking password field
         * case 'password':
         * break;
         *
         * case 'file':
         * if (empty($form_values[$key])) continue;
         * switch ($form[$key]['#widget']) {
         * case 'image':
         * $style = isset($value['#style']) ? $value['#style'] : 'thumbnail';
         * $form[$key]['#value'] = $form_values[$key];
         * $form[$key]['#prefix'] = lks_render('form_prefix-image', [
         * 'images' => (array) $form_values[$key],
         * 'style' => $style,
         * 'delete_link' => $empty_field_ajax_url,
         * ]);
         *
         * if (empty($form[$key]['#description'])) {
         * $form[$key]['#description'] = '';
         * }
         * break;
         *
         * case 'file':
         * break;
         * }
         * break;
         *
         * default:
         * if ($empty_field_ajax_url) {
         * $form[$key]['#suffix'] = !empty($form[$key]['#suffix']) ? $form[$key]['#suffix'] . $empty_field_ajax_url : $empty_field_ajax_url;
         * }
         *
         * $form[$key]['#value'] = $form_values[$key];
         * }
         * }
         * else {
         * self::setValues($form_id, $form[$key], $form_values[$key]);
         * }
         * }
         * break;
         * }
         * }
         */
    }

    private static function buildBeforeCache($form)
    {
        self::buildFields($form->fields, $form->error);
        self::buildFields($form->actions, $form->error);
    }

    private static function buildFields(&$fields, &$error)
    {
        $sort_weight = [];
        $sort_index = [];
        $index = 0;
        foreach ($fields as $key => $value) {
            $is_sort = true;
            
            // Don't care with #validate, #submit...
            if (isset($value['#type'])) {
                $fields[$key] = self::buildItem($value, $error);
                if (isset($error[$key])) {
                    unset($error[$key]);
                }
                
                // Use for sort
                $weight = isset($value['#weight']) ? $value['#weight'] : 0;
            } else {
                $is_sort = false;
                unset($fields[$key]);
            }
            
            // Use for sort
            if ($is_sort) {
                $sort_weight[$key] = $weight;
                $sort_index[$key] = $index;
                $index ++;
            }
            
            if (isset($value['#children'])) {
                self::buildFields($value['#children'], $error);
            }
        }
        
        // Sort by weight
        array_multisort($sort_weight, SORT_ASC, $sort_index, SORT_ASC, $fields);
    }

    private static function buildAfterCache($form)
    {}

    private static function itemInit(&$item)
    {
        $item['#id'] = isset($item['#id']) ? $item['#id'] : 'fii_' . lks_str_slug($item['#name']); // fii = form item id
        
        $item['#attributes'] = isset($item['#attributes']) ? $item['#attributes'] : [];
        $item['#attributes']['id'] = isset($item['#attributes']['id']) ? $item['#attributes']['id'] : $item['#id'] . '_field';
        $item['#attributes']['class'] = isset($item['#attributes']['class']) ? $item['#attributes']['class'] : '';
        $item['#class'] = 'form_item form_item_' . $item['#type'] . ' form_item_' . lks_str_slug($item['#name']) . (isset($item['#class']) ? ' ' . $item['#class'] : '');
        $item['#value'] = isset($item['#value']) ? $item['#value'] : '';
    }

    public static function buildItem($item, &$error = [])
    {
        self::itemInit($item);
        
        // Special field
        switch ($item['#type']) {
            case 'checkbox':
            case 'radio':
                $item['#checked'] = isset($item['#checked']) ? $item['#checked'] : false;
                break;
            
            case 'select':
                $item['#attributes']['class'] .= ' form-control';
                $item['#options'] = isset($item['#options']) ? $item['#options'] : [];
                break;
            
            case 'radios':
            case 'checkboxes':
                unset($item['#attributes']['id']);
                $item['#options'] = isset($item['#options']) ? $item['#options'] : [];
                break;
            
            case 'textarea':
                if (! empty($item['#rte_enable'])) {
                    $data = new \stdClass();
                    $data->item = $item;
                    event('lks.RTEEnable', $data);
                    $item = $data->item;
                }
                
                $item['#attributes']['class'] .= ' form-control';
                break;
            
            case 'date':
                if (empty($item['#config']['form_type'])) {
                    $item['#config']['form_type'] = 'datepicker';
                }
                
                switch ($item['#config']['form_type']) {
                    case 'select_group':
                        if (empty($item['#config']['group_format'])) {
                            $item['#config']['group_format'] = 'dmY';
                        }
                        break;
                }
                
                break;
            
            case 'submit':
                $item['#attributes']['class'] .= ' btn btn-primary';
                break;
            
            case 'button':
            case 'reset':
                $item['#attributes']['class'] .= ' btn btn-default';
                break;
            
            case 'file':
                break;
            
            default:
                $item['#attributes']['class'] .= ' form-control';
        }
        
        $data = new \stdClass();
        $data->item = $item;
        event('lks.formBuildItem: ' . $item['#type'], $data);
        $item = $data->item;
        
        // Error
        /*
         * if (isset($error[$item['#name']])) {
         * if (config('lks.form error message show in field', 1)) {
         * if (!isset($item['#error_message'])) {
         * $item['#error_message'] = null;
         * }
         * else {
         * $item['#error_message'] .= '<br />';
         * }
         * $item['#error_message'] = implode('<br />', $error[$item['#name']]);
         * unset($error[$item['#name']]);
         * }
         *
         * $item['#class'] .= ' error';
         * }
         */
        
        // Options callback
        if (! empty($item['#options_callback'])) {
            $options_callback = explode('@', $item['#options_callback']['class']);
            $item['#options_callback']['arguments'] = isset($item['#options_callback']['arguments']) ? (array) $item['#options_callback']['arguments'] : [];
            $item['#options'] = call_user_func_array([
                $lks->load($options_callback[0]),
                $options_callback[1]
            ], $item['#options_callback']['arguments']);
        }
        
        // AJAX
        if (isset($item['#ajax'])) {
            $js = array(
                'AJAX' => array(
                    $item['#attributes']['id'] => $item['#ajax']
                )
            );
            
            Asset::jsAdd($js, 'settings');
        }
        
        return $item;
    }

    public static function render($key, &$form)
    {
        $item = [];
        if (isset($form[$key])) {
            $item = $form[$key];
            unset($form[$key]);
        }
        
        if (count($item) && isset($item['#type'])) {
            if (isset($item['#theme'])) {
                $template = $item['#theme'];
                unset($item['#theme']);
            } else {
                $template_collection = [];
                
                if (isset($form['#id'])) {
                    $template_collection[] = 'form_item-' . $item['#type'] . '-' . $item['#name'] . '-' . $form['#id'];
                }
                $template_collection[] = 'form_item-' . $item['#type'] . '-' . $item['#name'];
                $template_collection[] = 'form_item-' . $item['#type'];
                $template_collection[] = 'form_item';
                
                $template = array_shift($template_collection);
                while (! View::exists($template) && count($template_collection)) {
                    $template = array_shift($template_collection);
                }
            }
            
            $item['#body'] = self::renderItem($item);
            
            return view($template, array(
                'element' => $item
            ));
        }
        
        return '';
    }

    public static function renderAll(&$fields)
    {
        $result = '';
        if (count($fields)) {
            foreach ($fields as $key => $value) {
                if (! empty($value['#type'])) {
                    $result .= self::render($key, $fields);
                }
                
                if (isset($value['#children'])) {
                    $result .= self::renderAll($value['#children']);
                }
            }
        }
        
        return $result;
    }

    public static function renderItem(&$element)
    {
        $result = '';
        
        /*
         * if (!isset($element['#attributes'])) {
         * kd($element);
         * }
         */
        
        switch ($element['#type']) {
            case 'markup':
                $result = $element['#value'];
                break;
            
            case 'file':
            case 'password':
                $result = LaravelForm::{$element['#type']}($element['#name'], $element['#attributes']);
                break;
            
            case 'button':
            case 'reset':
            case 'submit':
                $result = LaravelForm::{$element['#type']}($element['#value'], $element['#attributes']);
                break;
            
            case 'checkboxes':
                $checkboxes = '[]';
                $form_type = 'checkbox';
            case 'radios':
                $form_type = isset($form_type) ? $form_type : 'radio';
                $result = '';
                foreach ($element['#options'] as $key => $value) {
                    $result .= LaravelForm::$form_type($element['#name'] . (isset($checkboxes) ? $checkboxes : ''), $key, ($key == $element['#value'] ? true : false), $element['#attributes']);
                    $result .= '<label class="sublabel">' . $value . '</label>';
                }
                return $result;
            
            case 'checkbox':
            case 'radio':
                $result = LaravelForm::{$element['#type']}($element['#name'], $element['#value'], $element['#checked'], $element['#attributes']);
                break;
            
            case 'select':
                $result = LaravelForm::{$element['#type']}($element['#name'], $element['#options'], $element['#value'], $element['#attributes']);
                break;
            
            case 'hidden':
                $result = LaravelForm::{$element['#type']}($element['#name'], $element['#value'], $element['#attributes']);
            
            case 'text':
            case 'textarea':
            case 'email':
            case 'hidden':
                $result = LaravelForm::{$element['#type']}($element['#name'], $element['#value'], $element['#attributes']);
                break;
            
            default:
                $data = new \stdClass();
                $data->element = $element;
                event('lks.formRenderItem: ' . $element['#type'], $data);
                $element = $data->element;
                
                $result = LaravelForm::{$element['#type']}($element);
                break;
        }
        
        return $result;
    }

    public static function submit()
    {
        $form_id = Input::get('_form_id', false);
        $form_token = Input::get('_form_token', false);
        
        // Restore $form_items from cache
        $cache_name = lks_cache_name(__CLASS__ . '-build') . "-$form_id-$form_token";
        $form = Cache::get($cache_name);
        Cache::forget($cache_name);
        
        if (! count($form)) {
            return false;
        }
        
        $form_values = Input::all();
        
        // Validate this form
        $validate = true;
        if (! empty($form->validate)) {
            foreach ($form->validate as $value) {
                $segment = explode('@', $value);
                $data = array(
                    'form' => &$form,
                    'form_values' => &$form_values
                );
                if (! call_user_func_array([
                    new $segment[0](),
                    $segment[1]
                ], $data)) {
                    $validate = false;
                }
            }
        }
        
        if ($validate) {
            $validate = self::submitValidate($form, $form_values);
        }
        
        // Submit action
        if ($validate) {
            if (! empty($form->submit)) {
                foreach ($form->submit as $value) {
                    $segment = explode('@', $value);
                    $data = array(
                        'form' => &$form,
                        'form_values' => &$form_values
                    );
                    call_user_func_array([
                        new $segment[0](),
                        $segment[1]
                    ], $data);
                }
            }
            
            if ($form->message) {
                Output::messageAdd($form->message, 'success');
            }
            
            if ($form->redirect) {
                return redirect($form->redirect);
            }
        }         // Set default value for error form
        else {
            lks_cache_set($cache_name, $form);
            return false;
        }
        
        /*
         * //Close modal after submit
         * if (isset($form['#success'])) {
         * if (is_array($form['#success'])) {
         * $form['#success']['arguments'] = isset($form['#success']['arguments']) ? (array) $form['#success']['arguments'] : [];
         * $segment = explode('@', $form['#success']['class']);
         * $form['#success'] = call_user_func_array(array($lks->load($segment[0]), $segment[1]), $form['#success']['arguments']);
         * }
         *
         * $lks->response->addContent($form['#success']);
         * return false;
         * }
         */
        
        // Redirect after submit finalize
        /*
         * if ($redirect) {
         * switch ($lks->response->getOutputType()) {
         * case 'json':
         * case 'ajax':
         * case 'modal':
         * case 'iframe':
         * $data = array(
         * 'form_redirect' => $redirect,
         * );
         * $lks->response->addContentJSON($data);
         * $lks->response->setOutputType('json');
         * return false;
         *
         * default:
         * return lks_redirect($redirect);
         * }
         * }
         */
        
        return true;
    }

    private static function submitValidate(&$form, &$form_values)
    {
        $rules = [];
        $validate = self::submitValidateFields($form->fields, $form_values, $rules);
        
        if (count($rules)) {
            if ($form->connection) {
                $verifier = App::make('validation.presence');
                $verifier->setConnection($form->connection);
            }
            
            $validator = Validator::make($rules['value'], $rules['rule']);
            
            if ($form->connection) {
                $validator->setPresenceVerifier($verifier);
            }
            
            if ($validator->fails()) {
                $form->error = array_merge($form->error, lks_object_to_array(json_decode($validator->messages())));
                
                return false;
            }
        }
        
        return $validate;
    }

    private static function submitValidateFields(&$fields, &$form_values, &$rules)
    {
        $validate = true;
        
        foreach ($fields as $key => $value) {
            if (! self::submitValidateField($key, $fields[$key], $form_values, $rules)) {
                $validate = false;
            }
            
            if (isset($value['#children'])) {
                if (! self::submitValidateFields($fields[$key]['#children'], $form_values, $rules)) {
                    $validate = false;
                }
            }
        }
        
        return $validate;
    }

    private static function submitValidateField($key, &$field, &$form_values, &$rules)
    {
        $validate = true;
        
        // Remove disabled fields were edited by client
        if (! empty($field['#disabled'])) {
            $form_values[$key] = $field['#value'];
        } else {
            $field['#value'] = isset($form_values[$key]) ? $form_values[$key] : (isset($field['#value']) ? $field['#value'] : (isset($field['#default']) ? $field['#default'] : ''));
            
            if (isset($field['#type'])) {
                $event = new \stdClass();
                $event->field = $field;
                $event->validate = $validate;
                event('lks.formValidateField: ' . $field['#type'], $event);
                $field = $event->field;
                $validate = $event->validate;
            }
            
            $form_values[$key] = $field['#value'];
            
            if (isset($field['#validate'])) {
                $rules['value'][$key] = $form_values[$key];
                $rules['rule'][$key] = $field['#validate'];
            }
        }
        
        return $validate;
    }
}