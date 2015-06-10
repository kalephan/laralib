<?php
namespace Kalephan\URLAlias;

class URLAliasEvent
{

    public function uriAlterAlias(&$path)
    {
        if ($real = lks_instance_get()->load('\Kalephan\URLAlias\URLAliasEntity')->loadReal($path)) {
            $path = $real;
        }
    }

    public function makeURLAlterAlias(&$path)
    {
        if ($alias = lks_instance_get()->load('\Kalephan\URLAlias\URLAliasEntity')->loadAlias($path)) {
            $path = $alias;
        }
    }

    public function formAlterAddURLAlias($form_id, &$form)
    {
        if (! array_intersect(config('lks.urlalias roles', []), lks_user()->role)) {
            return false;
        }
        
        if (! in_array($form_id, config('lks.urlalias support', []))) {
            return false;
        }
        
        $structure = lks_instance_get()->load('\Kalephan\URLAlias\URLAliasEntity')->getStructure();
        
        $form['urlalias_alias'] = $structure->fields['alias'];
        $form['urlalias_alias']['#name'] = 'urlalias_alias';
        if (empty($form['urlalias_alias']['#attributes']['class'])) {
            $form['urlalias_alias']['#attributes']['class'] = 'urlalias';
        } else {
            $form['urlalias_alias']['#attributes']['class'] .= ' urlalias';
        }
        
        $form->submit[] = '\Kalephan\URLAlias\URLAliasEvent@formAlterAddURLAliasSubmit';
    }

    function formAlterAddURLAliasSubmit($form_id, &$form, &$form_values)
    {
        if (isset($form_values['urlalias_alias'])) {
            $lks = lks_instance_get();
            $structure = $lks->load($form_values['_entity'])->getStructure();
            
            $real = str_replace('%', $form_values[$structure->id], $structure['#action_links']['read']);
            
            $lks->load('\Kalephan\URLAlias\URLAliasEntity')->make($real, $form_values['urlalias_alias']);
        }
    }

    public function formValueAlterAddURLAlias($form_id, &$form, &$form_values)
    {
        $user = lks_user();
        if (! isset($user->role) || ! array_intersect(config('lks.urlalias roles', []), $user->role)) {
            return false;
        }
        
        $suport = config('lks.urlalias support alter value', []);
        if (! in_array($form_id, $suport)) {
            return false;
        }
        
        $lks = lks_instance_get();
        $structure = $lks->load($form['_entity']['#value'])->getStructure();
        
        $url_real = str_replace('%', $form_values[$structure->id], $structure['#action_links']['read']);
        
        $alias = $lks->load('\Kalephan\URLAlias\URLAliasEntity')->loadAlias($url_real);
        
        if (! empty($alias->alias)) {
            $form_values['urlalias_alias'] = $alias->alias;
            $form['urlalias_alias']['#disabled'] = true;
            $form['urlalias_alias']['#attributes']['disabled'] = 'disabled';
        }
    }
}