<?php
namespace Chovip\Ec;

class ProductEvent
{

    public function alterEntityStructureEcProduct(&$structure)
    {
        $structure->fields['label'] = array(
            '#name' => 'label',
            '#title' => 'Số thứ tự',
            '#type' => 'text'
        );
    }
}