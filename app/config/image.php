<?php

return array(
    /**
     * Librería para manipular imagenes
     */
    'library'     => 'imagick',

    'upload_dir'  => 'uploads',
    'upload_path' => public_path() . '/uploads/',
    'quality'     => 85,

    /**
     * Recipes
     * width,height,crop,quality
     */
    'recipes' => array(
        'profile'  => array(500, 500, false,  80),
        'profile.thumb'  => array(50, 50, true,  80),
        'profile.preview' => array(256, 500, false, 90),
    ),
);