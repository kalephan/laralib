<?php
namespace Kalephan\Location;

use Kalephan\Category\CategoryEntity;
use Kalephan\Core\Form;
use Illuminate\Support\Facades\Session;

class LocationEntity extends CategoryEntity
{

    function changeProvince($lks, $location_id)
    {
        if (is_numeric($location_id)) {
            $location_title = $this->loadEntity($location_id);
            
            if (isset($location_title->title)) {
                $location_title = $location_title->title;
            } else {
                $location_id = 0;
                $location_title = lks_lang('Toàn Quốc');
            }
            
            Session::set('location', [
                'id' => $location_id,
                'title' => $location_title
            ]);
        }
        
        return lks_redirect('/');
    }

    public function getDistrict($lks)
    {
        $parent = $lks->request->query('province_name', 'province_id');
        $parent = $lks->request->query($parent);
        $parent = $parent ? $parent : 0;
        
        $value = $lks->request->query('district_value', 'district_id_value');
        $value = $lks->request->query($value, 0);
        
        $form = [];
        $district_name = $lks->request->query('district_name', 'district_id');
        
        $form[$district_name] = array(
            '#name' => $district_name,
            '#type' => 'select',
            '#options_callback' => array(
                'class' => '\Kalephan\Location\LocationEntity@loadOptionsAll',
                'arguments' => array(
                    'id' => 'location_district',
                    'parent' => $parent,
                    'select_text' => lks_lang('--- Quận/Huyện ---')
                )
            ),
            '#value' => $value,
            '#validate' => 'required|numeric'
        );
        
        $form[$district_name] = Form::buildItem($form[$district_name]);
        $lks->response->addContent(Form::render($district_name, $form));
    }
}