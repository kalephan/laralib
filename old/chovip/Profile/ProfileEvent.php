<?php
namespace Chovip\Profile;

class ProfileEvent
{

    function alterEntityStructureProfile(&$structure)
    {
        $structure->fields['province_id'] = array(
            '#name' => 'province_id',
            '#title' => lks_lang('Khu vực'),
            '#type' => 'select',
            '#reference' => array(
                'name' => 'category',
                'class' => '\Kalephan\Category\CategoryEntity'
            ),
            '#options_callback' => array(
                'class' => '\Kalephan\Category\CategoryEntity@loadOptionsAll',
                'arguments' => array(
                    'id' => 'location_province',
                    'parent' => '',
                    'select_text' => lks_lang('--- Tỉnh/Thành ---')
                )
            ),
            '#ajax' => array(
                'path' => 'location/district',
                'wrapper' => 'fii_district_id',
                'autoload' => 1
            ),
            '#list_hidden' => true,
            '#required' => true,
            '#validate' => 'required|numeric'
        );
        
        $structure->fields['district_id'] = array(
            '#name' => 'district_id',
            '#type' => 'select',
            '#reference' => array(
                'name' => 'category',
                'class' => '\Kalephan\Category\CategoryEntity'
            ),
            '#options' => array(
                '' => lks_lang('--- Quận/Huyện ---')
            ),
            '#list_hidden' => true,
            '#required' => true,
            '#validate' => 'required|numeric'
        );
        
        $structure->fields['mobile'] = array(
            '#name' => 'mobile',
            '#title' => lks_lang('Điện thoại di động'),
            '#type' => 'text',
            '#required' => true,
            '#attributes' => array(
                'data-required' => ''
            ),
            '#error_message' => lks_lang('Trường này yêu cầu phải nhập.')
        );
        
        $structure->fields['cmnd'] = array(
            '#name' => 'cmnd',
            '#title' => lks_lang('CMND'),
            '#type' => 'text',
            '#required' => true,
            '#validate' => 'required|numeric|regex:/^[0-9]{9,12}$/',
            '#attributes' => array(
                'data-required' => '',
                'placeholder' => lks_lang('280819333'),
                'data-pattern' => '^[0-9]{9,12}$'
            ),
            '#description' => lks_lang('Số CMND từ 9 - 12 ký tự và chỉ có ký tự số'),
            '#error_message' => lks_lang('Số CMND từ 9 - 12 ký tự và chỉ có ký tự số'),
            '#list_hidden' => 1
        );
        
        $structure->fields['homephone'] = array(
            '#name' => 'homephone',
            '#title' => lks_lang('Điện thoại bàn'),
            '#type' => 'text',
            '#list_hidden' => 1
        );
    }

    public function alterShowUpdateForm($form_id, &$form)
    {
        $form['district_id_value'] = array(
            '#name' => 'district_id_value',
            '#type' => 'hidden'
        );
        
        $form->actions['reset'] = array(
            '#name' => 'reset',
            '#type' => 'reset',
            '#value' => lks_lang('Nhập lại')
        );
    }

    public function alterShowUpdateFormValue($form_id, &$form, &$form_values)
    {
        $profile_obj = lks_instance_get()->load('\Kalephan\Profile\ProfileEntity');
        $profile = $profile_obj->loadEntity($form_values['id']);
        
        $form_values['district_id_value'] = ! empty($profile->district_id) ? $profile->district_id : 0;
    }
}