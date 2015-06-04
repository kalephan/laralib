<?php
namespace Kalephan\LKS;

use Illuminate\Support\Facades\DB;

class EntityModelDB {
    public static function loadEntity($structure, $attributes = []) {
        $db = DB::table($structure->table);
        self::buildLoadEntityWhere($db, $structure, $attributes);

        return $db->first();
    }

    public static function loadEntityAll($structure, $attributes = [], &$count = 0) {
        $db = DB::table($structure->table);

        self::buildLoadEntityWhere($db, $structure, $attributes);
        self::buildLoadEntityOrder($db, $structure, $attributes);

        $db->select($structure->id);
        $count = $db->count();

        self::buildLoadEntitySelect($db, $structure, $attributes);
        self::buildLoadEntityPagination($db, $structure, $attributes);

        return $db->get();
    }

    public static function buildLoadEntitySelect(&$db, $structure, $attributes = []) {
        $attributes['select'] = isset($attributes['select']) ? $attributes['select'] : [$structure->id];
        $db->select($attributes['select']);
    }

    public static function buildLoadEntityWhere(&$db, $structure, $attributes = []) {
        $attributes['where'] = isset($attributes['where']) ? $attributes['where'] : [];

        // Get Filter
        $lks =& lks_instance_get();
        // When load language translate, we didn't load request method
        if (isset($lks->request)) {
            $filter = $lks->request->filter();
            if ((!isset($attributes['filter']) || $attributes['filter'] == true)
                && is_array($filter) && count($filter)
            ) {
                foreach ($filter as $key => $value) {
                    $attributes['where'][$key] = $value;
                }
            }
        }

        if (isset($attributes['where']['#entity_id'])) {
            $db->where($structure->id, '=', $attributes['where']['#entity_id']);
            unset($attributes['where']['#entity_id']);
        }

        if (isset($attributes['where']) && count($attributes['where'])) {
            foreach ($attributes['where'] as $key => $value) {
                if (isset($structure->fields[$key])) {
                    $key = explode(' ', $key);
                    $key[1] = isset($key[1]) ? $key[1] : '=';

                    $db->where($key[0], $key[1], $value);
                }
            }
        }
    }

    public static function buildLoadEntityPagination(&$db, $structure, $attributes = []) {
        $attributes['pagination'] = isset($attributes['pagination']) ? $attributes['pagination'] : [];

        if (isset($attributes['pagination']['start']) && isset($attributes['pagination']['length'])) {
            $db->skip($attributes['pagination']['start']);
            $db->take($attributes['pagination']['length']);
        }
    }

    public static function buildLoadEntityOrder(&$db, $structure, $attributes = []) {
        $attributes['order'] = isset($attributes['order']) ? $attributes['order'] : [];
        if (count($attributes['order'])) {
            foreach ($attributes['order'] as $key => $value) {
                $db->orderBy($key, $value);
            }
        }

        if (!isset($attributes['order']['weight']) && isset($structure->fields['weight'])) {
            $db->orderBy('weight', 'ASC');
        }

        if (!isset($structure['#order'])) {
            $structure['#order'] = array(
                'updated_at' => 'desc',
                'created_at' => 'desc',
                'title' => 'asc',
            );
        }

        foreach ($structure['#order'] as $key => $value) {
            if (!isset($attributes['order'][$key]) && isset($structure->fields[$key])) {
                $db->orderBy($key, $value);
            }
        }
    }

    public static function loadReference($field, $entity_id, $structure, $ref_structure) {
        $db = DB::table($structure->table . '_' . $ref_structure->table);

        $db->where('field', $field);
        $db->where($structure->table . '_' . $structure->id, $entity_id);
        $query = $db->get();

        $reference = [];
        foreach ($query as $row) {
            $reference[$row->{$ref_structure->table . '_' . $ref_structure->id}] = $row->{$ref_structure->table . '_' . $ref_structure->id};
        }

        return $reference;
    }

    public static function createEntity($entity, $structure) {
        $db = DB::table($structure->table);
        return $db->insertGetId(lks_object_to_array($entity));
    }

    public static function deleteEntity($entity_ids, $structure) {
        $db = DB::table($structure->table);

        if (!is_array($entity_ids)) {
            $entity_ids = array($entity_ids);
        }

        $db->whereIn($structure->id, $entity_ids);
        $db->delete();
    }

    public static function updateEntity($entity, $structure) {
        $db = DB::table($structure->table)
            ->where($structure->id, '=', $entity->{$structure->id})
            ->update(lks_object_to_array($entity));

        return $entity->{$structure->id};
    }

    public static function saveReference($reference, $entity_id, $structure) {
        foreach (array_keys($reference) as $key) {
            // Delete all of reference
            $db = DB::table($structure->table . '_' . $structure->fields[$key]['#reference']['name']);
            $db->where($structure->table . '_' . $structure->id, $entity_id);
            $db->where('field', $key);
            $db->delete();

            // Update new reference
            if (count($reference[$key])) {
                $ref_obj = lks_instance_get()->load($structure->fields[$key]['#reference']['class']);
                $ref_structure = $ref_obj->getStructure();

                $data = [];
                foreach ($reference[$key] as $value) {
                    if (is_object($value)) {
                        $value = $value->{$ref_structure->id};
                    }

                    $data[] = array(
                        'field' => $key,
                        $structure->table . '_' . $structure->id => $entity_id,
                        $ref_structure->table . '_' . $ref_structure->id => $value,
                    );
                }

                $db->insert($data);
            }
        }
    }
}