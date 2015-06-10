<?php
namespace Kalephan\SEO;

class SEOEvent
{

    public function fieldValidateTextarea(&$values, $field)
    {
        if (! $values || empty($field['#rte_enable'])) {
            return true;
        }
        
        $text = new \DOMDocument();
        @$text->loadHTML('<?xml encoding="UTF-8"?>' . $values); // LIBXML_HTML_NOIMPLIED
        
        if (config('lks.image lazy load', 1)) {
            $images = $text->getElementsByTagName('img');
            foreach ($images as $image) {
                $lazyload = $text->createAttribute('data-original');
                $lazyload->value = $image->getAttribute('src');
                $image->appendChild($lazyload);
                
                $image->removeAttribute('src');
                
                $class = $text->createAttribute('class');
                $class->value .= 'loading lazy';
                $image->appendChild($class);
            }
        }
        
        $anchors = $text->getElementsByTagName('a');
        foreach ($anchors as $anchor) {
            $nofollow = $text->createAttribute('rel');
            $nofollow->value .= 'nofollow';
            $anchor->appendChild($nofollow);
            
            $target = $text->createAttribute('target');
            $target->value .= '_blank';
            $anchor->appendChild($target);
        }
        
        $body = $text->getElementsByTagName('body')->item(0);
        
        $values = str_replace("</body>", '', str_replace("<body>", '', $text->saveHTML($body)));
        
        return true;
    }
}