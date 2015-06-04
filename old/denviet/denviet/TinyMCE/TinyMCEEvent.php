<?php
namespace Kalephan\TinyMCE;

class TinyMCEEvent {
    public function buildItemTextarea(&$item) {
        $item['#attributes']['id'] = $this->_getRTEId();
        $item['#class'] .= ' form_item_textarea_rte';

        lks_event_listen('assets.jsAlter', '\Kalephan\TinyMCE\TinyMCEEvent@loadTinyMCE');
    }

    public function loadTinyMCE(&$js) {
        if (!isset($js['custom']['tinymce'])) {
            $js['custom']['tinymce'] = config('lks.tinymce libraries path', '//tinymce.cachefly.net/4.1/tinymce.min.js');
            $js['inline']['tinymce'] = $this->_loadConfig();
        }
    }

    private function _loadConfig() {
        global $LKSTinyMCETinyMCEEvent_getRTEId;

        $config = array(
            'mode' => 'mode:"' . config('lks.tinymce config mode', 'exact') . '"',
            'elements' => 'elements:"' . implode(',', $LKSTinyMCETinyMCEEvent_getRTEId) . '"',
            'theme' => 'theme:"' . config('lks.tinymce config theme', 'modern') . '"',
            'language' => 'language:"' . config('lks.tinymce config language', 'vi') . '"',
            'language_url' => 'language_url:"' . config('lks.tinymce config language_url ', '/assets/libraries/tinymce/langs/vi.js') . '"',
            'relative_urls' => 'relative_urls:' . config('lks.tinymce config relative_urls', 'false'),
            'width' => 'width:"' . config('lks.tinymce config width', '100%') . '"',
            'height' => 'height:"' . config('lks.tinymce config height', 300) . '"',
            'plugins' => 'plugins:["' . config('lks.tinymce config plugins line 1', 'advlist autolink link image lists charmap hr anchor') . '","' . config('lks.tinymce config plugins line 2', 'searchreplace wordcount visualblocks visualchars') . '","' . config('lks.tinymce config plugins line 3', 'table contextmenu directionality emoticons paste textcolor code') . '"]',
            'toolbar1' => 'toolbar1:"' . config('lks.tinymce config toolbar1', 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ') . '"',
            'toolbar2' => 'toolbar2:"' . config('lks.tinymce config toolbar2', '| link unlink anchor | image emoticons charmap | forecolor backcolor | styleselect | visualblocks | code') . '"',
            'image_advtab' => 'image_advtab:' . config('lks.tinymce config image_advtab', 'true'),
            'external_plugins' => 'external_plugins:{' . implode(',', config('lks.tinymce config external_plugins', [])) . '}',
        );

        $fire_data = ['config' => &$config];
        event('tinyMCE.loadConfig', $fire_data);
        $config = $fire_data['config'];

        return 'tinyMCE.init({' . implode(',', $config) . '});';
    }

    private function _getRTEId() {
        global $LKSTinyMCETinyMCEEvent_getRTEId;

        if (empty($LKSTinyMCETinyMCEEvent_getRTEId)) {
            $next = 'rte';
            $LKSTinyMCETinyMCEEvent_getRTEId[] = $next;
        }
        else {
            $next = substr(end($LKSTinyMCETinyMCEEvent_getRTEId), 3);
            $next = 'rte' . ($next ? intval($next) + 1 : 1);
            $LKSTinyMCETinyMCEEvent_getRTEId[] = $next;
        }

        return $next;
    }
}