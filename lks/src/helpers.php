<?php
use Carbon\Carbon;
use Kalephan\LKS\Facades\Form;
use Kalephan\LKS\Facades\Output;
use Illuminate\Html\HtmlFacade as HTML;
use Illuminate\Html\FormFacade as LaravelForm;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

// https://api.drupal.org/api/drupal/includes!bootstrap.inc/function/drupal_array_merge_deep/7
function lks_array_merge_deep()
{
    $args = func_get_args();
    return _lks_array_merge_deep_array($args);
}

// https://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/drupal_array_merge_deep_array/7
function _lks_array_merge_deep_array($arrays)
{
    $result = [];

    foreach ($arrays as $array) {
        foreach ($array as $key => $value) {
            // Renumber integer keys as array_merge_recursive() does. Note that PHP
            // automatically converts array keys that are integer strings (e.g., '1')
            // to integers.
            if (is_integer($key)) {
                $result[] = $value;
            }            // Recurse when both values are arrays.
            elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                $result[$key] = _lks_array_merge_deep_array(array(
                    $result[$key],
                    $value
                ));
            }             // Otherwise, use the latter value, overriding any previous value.
            else {
                $result[$key] = $value;
            }
        }
    }

    return $result;
}

function lks_cache_name($name)
{
    $cache = Cache::get(__FUNCTION__, []);

    if (! isset($cache[$name])) {
        $cache[$name] = count($cache);
        Cache::forever(__FUNCTION__, $cache);
    }

    return $cache[$name];
}

function lks_cache_set($cache, $value, $expired = null)
{
    $expired = $expired ? $expired : Carbon::now()->addMinutes(config('lks.cache_ttl', 10));

    return Cache::put($cache, $value, $expired);
}

function lks_date2db($date)
{
    return date('Y-m-d H:i:s', $date);
}

function lks_entities2table($entities, $structure, $cols = [])
{
    $data = [
        'header' => [],
        'rows' => []
    ];

    $fields = $structure->fields;
    if ($cols) {
        $fields = array_intersect_key($fields, $cols);
    }

    foreach ($fields as $key => $field) {
        if (! empty($field['#not_listed'])) {
            unset($fields[$key]);
        } else {
            $data['header'][] = $field['#title'];
        }
    }

    $action_col = 0;
    if (count($structure->actions)) {
        $data['header'][] = lks_lang('Hoạt động');

        $actions = $structure->actions;

        if (isset($actions['create'])) {
            unset($actions['create']);
        }

        if (isset($actions['list'])) {
            unset($actions['list']);
        }

        $action_col = count($actions);
    }

    foreach ($entities as $entity) {
        $row = [];
        foreach ($fields as $key => $field) {
            if (isset($field['#reference'])) {
                if (!empty($entity->$key)) {
                    $row[] = $entity->$key->{$field['#reference']['title']};
                }
                else {
                    $row[] = '';
                }
            }
            else {
                $row[] = $entity->$key;
            }

        }

        if ($action_col) {
            $links = '';
            foreach ($actions as $key => $value) {
                $value['url'] = lks_entity_token_trans($value['url'], $entity, $structure);

                $links .= '<a href=" ' . lks_url($value['url']) . ' " clas="actions_link actions_'.$key.'">' . $value['title'] . '</a> ';
            }
            $row[] = $links;
        }

        $data['rows'][] = $row;
    }

    return $data;
}

function lks_entity_token_trans($string, $entity = null, $structure = null) {
    $trans_key = $trans_value = [];

    if ($structure) {
        $trans_key[] = '@structure-url_prefix';
        $trans_value[] = $structure->url_prefix;

        $trans_key[] = '@structure-title';
        $trans_value[] = $structure->title;
    }

    if ($entity) {
        $trans_key[] = '@id';
        $trans_value[] = $entity->{$structure->id};
    }

    if ($structure && $entity) {
        foreach ($structure->fields as $key => $value) {
            if ($key != $structure->id && (is_string($value) || is_numeric($value))) {
                $trans_key[] = "@$key";
                $trans_value[] = $value;
            }
        }
    }

    return str_replace($trans_key, $trans_value, $string);
}

function lks_form_close()
{
    return LaravelForm::close();
}

function lks_form_open($form)
{
    return LaravelForm::open($form);
}

function lks_form_render($key, $form)
{
    return Form::render($key, $form);
}

function lks_form_render_all($form)
{
    return Form::renderAll($form);
}

function lks_form_error($form)
{
    // Change [
    // 'email' => ['Invalid email'],
    // 'password' => ['Invalid password']
    // ]
    // To [
    // 'Invalid email',
    // 'Invalid password'
    // ]
    $data = [];
    foreach ($form->error as $value) {
        $data += $value;
    }

    return HTML::ul($data);
}

/* function lks_html_ul($list, $attributes = []) {
    return HTML::ul($list, $attributes);
} */

function lks_lang($line, $trans = [], $file = null)
{
    $tmp = $line;
    if ($file) {
        /*
         * $file = explode('.', $file);
         * if (isset($file[1])) {
         * $tmp = $file[1] . ".$tmp";
         * }
         * $tmp = $file[0] . ".$tmp";
         */
        $tmp = "$file.$tmp";
    }
    $text = Lang::get($tmp, $trans);

    if ($text == $tmp) {
        $text = $line;
    }

    return $text;
}

function lks_object_to_array($object)
{
    if ($object) {
        return json_decode(json_encode($object), true);
    }

    return [];
}

function lks_str_slug($text)
{
    $text = str_replace('\\', '-', $text);

    $text = strip_tags($text); // Strip html & php tag
    $text = lks_str_utf82ascii($text); // Convert utf8 to similar ascii character
    $text = strtolower($text); // Change uppercase to lowercase
    $text = preg_replace('/[^a-z0-9\-_\/]/u', '-', $text); // Replace unexpected character and full trim "-" characters
    $text = preg_replace('/(?:(?:^|\n)-+|-+(?:$|\n))/u', '', $text);
    $text = preg_replace('/-+/u', '-', $text);

    return $text;
}

function lks_str_utf82ascii($text)
{
    $text = preg_replace('/[áàảãạâấầẩẫậăắằẳẵặªä]/u', 'a', $text);
    $text = preg_replace('/[ÁÀẢÃẠÂẤẦẨẪẬĂẮẰẲẴẶÄ]/u', 'A', $text);
    $text = preg_replace('/[éèẻẽẹêếềểễệë]/u', 'e', $text);
    $text = preg_replace('/[ÉÈẺẼẸÊẾỀỂỄỆË]/u', 'E', $text);
    $text = preg_replace('/[íìỉĩịîï]/u', 'i', $text);
    $text = preg_replace('/[ÍÌỈĨỊÎÏ]/u', 'I', $text);
    $text = preg_replace('/[óòỏõọôốồổỗộơớờởỡợºö]/u', 'o', $text);
    $text = preg_replace('/[ÓÒỎÕỌÔỐỒỔỖỘƠỚỜỞỠỢÖ]/u', 'O', $text);
    $text = preg_replace('/[úùủũụưứừửữựûü]/u', 'u', $text);
    $text = preg_replace('/[ÚÙỦŨỤƯỨỪỬỮỰÛÜ]/u', 'U', $text);
    $text = preg_replace('/[ýỳỷỹỵ]/u', 'y', $text);
    $text = preg_replace('/[ÝỲỶỸỴ]/u', 'Y', $text);
    $text = preg_replace('/[đ]/u', 'd', $text);
    $text = preg_replace('/[Đ]/u', 'D', $text);

    return $text;
}

// Drupal 7 function
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
function lks_table($variable)
{
    $header = isset($variable['header']) ? $variable['header'] : [];
    $rows = isset($variable['rows']) ? $variable['rows'] : [];
    $attributes = isset($variable['attributes']) ? $variable['attributes'] : [];
    $caption = isset($variable['caption']) ? $variable['caption'] : '';
    $colgroups = isset($variable['colgroups']) ? $variable['colgroups'] : [];
    // $sticky = isset($variable['sticky']) ? $variable['sticky'] : false;
    $empty = isset($variable['empty']) ? $variable['empty'] : '';

    // Add sticky headers, if applicable.
    /*
     * if (count($header) && $sticky) {
     * drupal_add_js('misc/tableheader.js');
     * // Add 'sticky-enabled' class to the table to identify it for JS.
     * // This is needed to target tables constructed by this function.
     * $attributes['class'] = 'sticky-enabled';
     * }
     */

    $attributes['class'] = ! empty($attributes['class']) ? $attributes['class'] . ' ' : '';
    $attributes['class'] .= 'table';
    $output = '<table' . HTML::attributes($attributes) . ">\n";

    if (isset($caption)) {
        $output .= '<caption>' . $caption . "</caption>\n";
    }

    // Format the table columns:
    if (count($colgroups)) {
        foreach ($colgroups as $number => $colgroup) {
            $attributes = [];

            // Check if we're dealing with a simple or complex column
            if (isset($colgroup['data'])) {
                foreach ($colgroup as $key => $value) {
                    if ($key == 'data') {
                        $cols = $value;
                    } else {
                        $attributes[$key] = $value;
                    }
                }
            } else {
                $cols = $colgroup;
            }

            // Build colgroup
            if (is_array($cols) && count($cols)) {
                $output .= ' <colgroup' . HTML::attributes($attributes) . '>';
                $i = 0;
                foreach ($cols as $col) {
                    $output .= ' <col' . HTML::attributes($col) . ' />';
                }
                $output .= " </colgroup>\n";
            } else {
                $output .= ' <colgroup' . HTML::attributes($attributes) . " />\n";
            }
        }
    }

    // Add the 'empty' row message if available.
    if (! count($rows) && $empty) {
        $header_count = 0;
        foreach ($header as $header_cell) {
            if (is_array($header_cell)) {
                $header_count += isset($header_cell['colspan']) ? $header_cell['colspan'] : 1;
            } else {
                $header_count ++;
            }
        }
        $rows[] = array(
            array(
                'data' => $empty,
                'colspan' => $header_count,
                'class' => [
                    'empty',
                    'message'
                ]
            )
        );
    }

    // Format the table header:
    if (count($header)) {
        // $ts = tablesort_init($header);
        // HTML requires that the thead tag has tr tags in it followed by tbody
        // tags. Using ternary operator to check and see if we have any rows.
        $output .= (count($rows) ? ' <thead><tr>' : ' <tr>');
        foreach ($header as $cell) {
            // $cell = tablesort_header($cell, $header, $ts);
            $output .= lks_table_cell($cell, TRUE);
        }
        // Using ternary operator to close the tags based on whether or not there are rows
        $output .= (count($rows) ? " </tr></thead>\n" : "</tr>\n");
    }
    /*
     * else {
     * $ts = [];
     * }
     */

    // Format the table rows:
    if (count($rows)) {
        $output .= "<tbody>\n";
        $flip = array(
            'even' => 'odd',
            'odd' => 'even'
        );
        $class = 'even';
        foreach ($rows as $number => $row) {
            // Check if we're dealing with a simple or complex row
            if (isset($row['data'])) {
                $cells = $row['data'];
                $no_striping = isset($row['no_striping']) ? $row['no_striping'] : FALSE;

                // Set the attributes array and exclude 'data' and 'no_striping'.
                $attributes = $row;
                unset($attributes['data']);
                unset($attributes['no_striping']);
            } else {
                $cells = $row;
                $attributes = [];
                $no_striping = FALSE;
            }

            if (count($cells)) {
                // Add odd/even class
                if (! $no_striping) {
                    $class = $flip[$class];
                    $attributes['class'] = $class;
                }

                // Build row
                // kd($attributes);
                $output .= ' <tr' . HTML::attributes($attributes) . '>';
                $i = 0;
                foreach ($cells as $cell) {
                    // $cell = tablesort_cell($cell, $header, $ts, $i++);
                    $output .= lks_table_cell($cell);
                }
                $output .= " </tr>\n";
            }
        }
        $output .= "</tbody>\n";
    }

    $output .= "</table>\n";
    return $output;
}

// Drupal 7 function
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/_theme_table_cell/7
function lks_table_cell($cell, $header = FALSE)
{
    $attributes = '';

    if (is_array($cell)) {
        $data = isset($cell['data']) ? $cell['data'] : '';
        $header |= isset($cell['header']);
        unset($cell['data']);
        unset($cell['header']);
        $attributes = HTML::attributes($cell);
    } else {
        $data = $cell;
    }

    if ($header) {
        $output = "<th$attributes>$data</th>";
    } else {
        $output = "<td$attributes>$data</td>";
    }

    return $output;
}

function lks_url($url, $parameters = [], $secure = null)
{
    $link = new stdClass();
    $link->url = $url;
    $link->parameters = $parameters;
    $link->secure = $secure !== null ? $secure : config('lks.link_secure', false);
    event('lks.makeLink', $link);

    return url($link->url, $link->parameters, $link->secure);
}

function lks_validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function lks_view($view = null, $data = [], $mergeData = [])
{
    return view(Output::page($view), $data, $mergeData);
}

function lks_view_paths()
{
    $paths = array_merge(
        config('view.paths', []),
        [
            config('lks.theme_default', realpath(base_path('vendor/kalephan/lks/views')))
        ],
        Output::path(),
        [config('lks.theme_engine', realpath(base_path('vendor/kalephan/lks/views')))]
    );

    return array_unique($paths);
}

// lks_url2alias('search/product?c2=808&brand=viet-tien', 'Quan ao')
// ==> quan-ao_search.product:c2.808;brand.viet-tien
/*
 * function lks_url2alias($url = null, $slug_text = null, $query = null) {
 * if ($url) {
 * $url = trim($url, '/') . '/';
 * }
 *
 * if ($slug_text) {
 * $slug_text = lks_str_slug($slug_text, '-');
 * $slug_text = str_replace('_', '-', $slug_text);
 * }
 *
 * if ($query) {
 * $query = str_replace('/', '.', $query);
 * $query = str_replace('?', ':', $query);
 * $query = str_replace('&', ';', $query);
 * $query = str_replace('=', '.', $query);
 * $query = "_$query" ;
 * }
 *
 * return trim($url . $slug_text . $url, '/');
 * }
 */

// quan-ao_search.product:.808;brand.fashion:viet_tien;sale.25.5
// ==> search/product?.808=&brand=fashion:viet_tien&sale=25.5
/*
 * function lks_alias2url($alias) {
 * //$alias = quan-ao_search.product:.808;brand.fashion:viet_tien;sale.25.5
 *
 * $_pos = strpos($alias, '_');
 * $_pos = $_pos !== false ? $_pos + 1 : 0;
 * $alias = substr($alias, $_pos);
 * //$alias = search.product:.808;brand.fashion:viet_tien;sale.25.5
 *
 * $alias = explode(':', $alias);
 * //$alias[0] = search.product
 * //$alias[1] = .808;brand.fashion
 * //$alias[2] = viet_tien;sale.25.5
 *
 * // URI
 * $url = str_replace('.', '/', $alias[0]);
 * //$url = search/product
 *
 * unset($alias[0]);
 * //$alias[0] = .808;brand.fashion
 * //$alias[1] = viet_tien;sale.25.5
 *
 * // Query
 * if (count($alias)) {
 * $alias = implode(':', $alias);
 * //$alias = .808;brand.fashion:viet_tien;sale.25.5
 *
 * $alias = explode(';', $alias);
 * //$alias[0] = .808
 * //$alias[1] = brand.fashion:viet_tien
 * //$alias[2] = sale.25.5
 *
 * $url .= '?';
 * //$url = search/product?
 *
 * $i = 0;
 * foreach ($alias as $value) {
 * if ($i) {
 * $url .= '&';
 * //Foreach 2: $url = search/product?.808=&
 * //Foreach 3: $url = search/product?.808=&brand=fashion:viet_tien&
 * }
 * else {
 * $i = 1;
 * //Foreach 1:$url = search/product?
 * }
 *
 * //Foreach 1: $value = .808
 * //Foreach 2: $value = brand.fashion:viet_tien
 * //Foreach 3: $value = sale.25.5
 *
 * $dotpos = strpos($value, '.');
 * $dotpos = $dotpos ? $dotpos : strlen($value);
 * $url .= substr($value, 0, $dotpos) . '=';
 * //Foreach 1: $url = search/product?.808=
 * //Foreach 2: $url = search/product?.808=&brand=
 * //Foreach 3: $url = search/product?.808=&brand=fashion:viet_tien&sale=
 *
 * if ($dotpos) {
 * $url .= substr($value, $dotpos+1);
 * //Foreach 2: $url = search/product?.808=&brand=fashion:viet_tien
 * //Foreach 3: $url = search/product?.808=&brand=fashion:viet_tien&sale=25.5
 * // This is result
 * }
 * }
 * }
 *
 * return $url;
 * }
 */


