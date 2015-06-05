<?php
namespace Kalephan\Ec\Coupon;

class CouponEvent
{

    public function structureAlterProduct(&$structure)
    {
        $structure->fields['coupon_value'] = array(
            '#name' => 'coupon_value',
            '#title' => lks_lang('Khuyến mãi'),
            '#type' => 'text'
        );
        
        $structure->fields['coupon_type'] = array(
            '#name' => 'coupon_type',
            '#title' => lks_lang('Kiểu khuyến mãi'),
            '#type' => 'select',
            '#options' => array(
                'percent' => lks_lang('%'),
                'value' => lks_lang('vnđ')
            ),
            '#default' => 'percent'
        );
        $structure->fields['coupon_start'] = array(
            '#name' => 'coupon_start',
            '#title' => lks_lang('Thời điểm bắt đầu'),
            '#type' => 'text',
            '#widget' => 'date_timestamp'
        );
        $structure->fields['coupon_end'] = array(
            '#name' => 'coupon_end',
            '#title' => lks_lang('Thời điểm kết thúc'),
            '#type' => 'text',
            '#widget' => 'date_timestamp'
        );
    }
}