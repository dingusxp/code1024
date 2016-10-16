<?php

/**
 * picstore
 */
return array(

    /**
     * storage
     */
    'storage' => array(
        'engine' => 'Local',
        'option' => array(
            'base_path' => dirname(APP_PATH).'/www/asset',
            'base_url' => '/asset',
        ),
    ),

    // cate
    'cate' => array(

        // post pic
        'post_pic' => array(
            'type_limit' => array(Image::TYPE_GIF, Image::TYPE_JPG, Image::TYPE_PNG),
            'size_limit' => 2000000,
            'convert_type' => Image::TYPE_JPG,
            'watermark' => array(
            ),
            'thumb' => array(
                'list' => array('width' => 512, 'height' => 512, 'resize_mode' => Image::RESIZE_STRETCH),
                'preview' => array('width' => 160, 'height' => 160, 'resize_mode' => Image::RESIZE_STRETCH),
            ),
        ),
   ),
);