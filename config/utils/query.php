<?php

if (!function_exists('getPagination')) {
    function getPagination($query): array
    {
        $page = abs($query['page']) ?: 1;
        $limit = abs($query['count']) ?: 10;
        $skip = ($page - 1) * $limit;

        return [
            'skip' => $skip,
            'limit' => $limit
        ];
    }
    return [
        'getPagination' => 'getPagination'
    ];
}
