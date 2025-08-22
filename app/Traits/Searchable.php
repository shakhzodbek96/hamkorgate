<?php

namespace App\Traits;

trait Searchable
{

    public static function tiyin():array
    {
        return [];
    }
    public static function searchEngine()
    {
        $tiyin = self::tiyin();
        $request = request();
        $query = self::query();
        $table = (new self())->getTable();
        foreach ((new self())->fillable as $item) {
            $operator = $item . '_operator';

            if (!$request->filled($item)) continue;

            $select = isset($tiyin[$item]) ? $request->$item * 100 : $request->$item;
            $select_pair = $request->has($item . '_pair') ?
                (isset($tiyin[$item]) ? $request->{$item . '_pair'} * 100 : $request->{$item . '_pair'})
                : null;

            if ($request->filled($operator)) {
                switch (strtolower($request->$operator)) {
                    case 'between':
                        if ($select_pair !== null) {
                            $query->whereBetween("$table.$item", [$select, $select_pair]);
                        }
                        break;

                    case 'wherein':
                        $query->whereIn("$table.$item", array_map('trim', explode(',', $select)));
                        break;

                    case 'like':
                        $likeValue = strpos($select, '%') === false ? '%' . $select . '%' : $select;
                        $query->whereRaw("$table.$item ILIKE ?", $likeValue);
                        break;

                    default:
                        $query->where("$table.$item", $request->$operator, $select);
                        break;
                }
            } else {
                $query->where("$table.$item", $select);
            }
        }
        return $query;
    }

    public static function search(array $params,$multiple = false)
    {
        $params = array_filter($params,function ($item){
            return $item !== null;
        });
        $obj = new self();
        $attributes = array_merge($obj->fillable,['id']);
        $query = self::whereNotNull('id');
        foreach ($attributes as $attribute) {
            if (isset($params[$attribute])){
                if (isset($params[$attribute.'_operator']))
                    $query->where($attribute,$params[$attribute.'_operator'],$params[$attribute]);
                else
                {
                    if ($multiple)
                        $query->where($attribute,'like',"%".$params[$attribute]."%");
                    elseif (is_bool($params[$attribute]))
                        $query->where($attribute,boolval($params[$attribute]));
                    else
                        $query->where($attribute,'like',$params[$attribute]."%");
                }
            }
        }
        return $query;
    }
}
