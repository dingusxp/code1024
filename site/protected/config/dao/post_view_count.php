<?php

/*
 * post_view_count 表
 */
return array(
    'table' => array(
        'name' => 'post_view_count',
        'pk' => 'id',
        'fields' => array('id', 'count'),
        'required' => array('id', 'count'),
        'default' => array(
            'count' => 0,
        ),
        'encode_fields' => array(),
    ),
);