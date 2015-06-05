<?php
namespace Kalephan\Ec\Category;

use Kalephan\Category\CategoryEntity as LKSCategoryEntity;
use Kalephan\Core\Form;
use Illuminate\Support\Facades\Session;

class CategoryEntity extends LKSCategoryEntity
{

    public function getChildren($lks)
    {
        /*
         * $group = $lks->request->query('group_name');
         * $group = $group ? $group : '';
         *
         * $parent = $lks->request->query('parent_name');
         * $parent = $lks->request->query($parent);
         * $parent = $parent ? $parent : 0;
         *
         * $value = $lks->request->query('value_name');
         * $value = $lks->request->query($value);
         * $value = $value ? $value : 0;
         *
         * $me = $lks->request->query('me_name');
         *
         * $form = [];
         * $form[$me] = array(
         * '#name' => $me,
         * '#type' => 'select',
         * '#options_callback' => array(
         * 'class' => '\Kalephan\Category\CategoryEntity@loadOptionsAll',
         * 'arguments' => array(
         * 'id' => $group,
         * 'parent' => $parent,
         * ),
         * ),
         * '#value' => $value,
         * '#attributes' => array(
         * 'size' => 20,
         * ),
         * );
         *
         * $form[$me] = Form::buildItem($form[$me]);
         *
         * $lks->response->addContent(lks_form_render($me, $form));
         */
        $group = $lks->request->query('group_name');
        $group = $group ? $group : '';
        
        $parent = $lks->request->query('parent_name', 'parent');
        $parent = $lks->request->query($parent);
        $parent = $parent ? $parent : 0;
        
        $value = $lks->request->query('child_value', 'child_value');
        $value = $lks->request->query($value, 0);
        
        $form = [];
        $child_name = $lks->request->query('child_name', 'child_name');
        
        /*
         * $form[$district_name] = array(
         * '#name' => $district_name,
         * '#type' => 'select',
         * '#options_callback' => array(
         * 'class' => '\Kalephan\Location\LocationEntity@loadOptionsAll',
         * 'arguments' => array(
         * 'id' => 'location_district',
         * 'parent' => $parent,
         * 'select_text' => lks_lang('--- Quận/Huyện ---'),
         * ),
         * ),
         * '#value' => $value,
         * '#validate' => 'required|numeric',
         * );
         */
        
        $form[$child_name] = array(
            '#name' => $child_name,
            '#type' => 'select',
            '#options_callback' => array(
                'class' => '\Kalephan\Category\CategoryEntity@loadOptionsAll',
                'arguments' => array(
                    'id' => $group,
                    'parent' => $parent
                )
            ),
            '#attributes' => array(
                'size' => 20
            ),
            '#value' => $value
        );
        
        $form[$child_name] = Form::buildItem($form[$child_name]);
        $lks->response->addContent(Form::render($child_name, $form));
    }
}