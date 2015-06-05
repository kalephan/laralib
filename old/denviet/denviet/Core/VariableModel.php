<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class VariableModel
{

    public static function get($key, $default = null)
    {
        $db = DB::table('variables');
        $db->select('value');
        $db->where('name', $key);
        $value = $db->first();
        
        if (isset($value->value)) {
            if (strpos($value->value, ':')) {
                $default = unserialize($value->value);
            } else {
                $default = $value->value;
            }
        }
        
        return $default;
    }

    public static function set($key, $value)
    {
        $value = serialize($value);
        
        if (self::get($key) !== null) {
            DB::table('variables')->where('name', '=', $key)->update(array(
                'value' => $value
            ));
        } else {
            DB::table('variables')->insert(array(
                'name' => $key,
                'value' => $value
            ));
        }
    }
}