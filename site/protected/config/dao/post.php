<?php

/*
 * post 表
 */
return array(
    'table' => array(
        'name' => 'post',
        'pk' => 'id',
        'fields' => array('id', 'title', 'author', 'email', 'tpl', 'code', 'pic_id', 'create_time'),
        'required' => array('title', 'code', 'pic_id'),
        'default' => array(
            'author' => '',
            'email' => '',
            'create_time' => '0000-00-00 00:00:00',
        ),
        'encode_fields' => array(),
    ),
);