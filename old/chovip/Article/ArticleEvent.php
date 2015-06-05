<?php
namespace Chovip\Article;

class ArticleEvent
{

    function structureAlterArticle(&$structure)
    {
        $structure->fields['category_id'] = array(
            '#name' => 'category_id',
            '#title' => lks_lang('Danh mục'),
            '#type' => 'select',
            '#reference' => array(
                'name' => 'category',
                'class' => '\Kalephan\Category\CategoryEntity'
            ),
            '#options_callback' => array(
                'class' => '\Kalephan\Category\CategoryEntity@loadOptionsAll',
                'arguments' => array(
                    'group_id' => 'article_category',
                    'parent' => null,
                    'select_text' => lks_lang('--- Danh mục ---')
                )
            ),
            '#list_hidden' => true,
            '#validate' => 'required|numeric',
            '#required' => true,
            '#attributes' => array(
                'date-required' => 'true'
            ),
            '#error_message' => lks_lang('Trường này yêu cầu phải nhập')
        );
    }
}