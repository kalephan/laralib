<?php
namespace Kalephan\RFM;

class RFM {
    public static function renderUploadButton($id, $title = null, $attributes = []) {
        lks_instance_get()->asset->jsAdd(['lks.rfm' => '/assets/js/lks.rfm.js']);

        $attributes['data-src'] = config('lks.rfm config dialog path', '/rfm/filemanager/dialog.php') . '?type=2&field_id=' . $id . '&crossdomain=' . config('lks.rfm config crossdomain', 0);
        $attributes['class'] = (!empty($attributes['class']) ? $attributes['class'] . ' ' : '') . 'modal-remote';

        return lks_template_anchor_bootstrap_modal('#', $title ? $title : lks_lang('Browse'), $attributes);
    }
}
