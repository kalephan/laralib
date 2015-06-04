<?php
namespace Kalephan\RFM;

class RFMEvent {
    public function addToTinyMCE(&$config) {
        $config['external_filemanager_path'] = 'external_filemanager_path:"' . config('lks.rfm tinymce config external_filemanager_path', '/rfm/filemanager/') . '"';
        $config['filemanager_crossdomain'] = 'filemanager_crossdomain:' . config('lks.rfm tinymce config filemanager_crossdomain', 'false');
        $config['filemanager_title'] = 'filemanager_title:"' . config('lks.rfm tinymce config filemanager_title', 'Thư Viện Ảnh') . '"';
    }

    public function addToFileBrowse(&$element) {
        if ($element['#widget'] == 'image') {
            $element['#type'] = 'hidden';
            unset($element['#validate']);

            $element['#class'] .= ' rfm_image_upload';
            $element['#attributes']['class'] .= ' rfm_image_upload_field';
            $element['#prefix'] .= '<img src="" class="rfm_image_upload_img" />';
            $element['#suffix'] .= RFM::renderUploadButton($element['#attributes']['id'], lks_lang('Up Ảnh'), ['class' => 'btn btn-default btn-sm']);
        }
    }
}