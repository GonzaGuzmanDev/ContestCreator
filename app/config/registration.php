<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | User registration
    |--------------------------------------------------------------------------
    |
    | Activa el registro de usuarios
    |
    */

    'enabled' => true,


    /*
    |--------------------------------------------------------------------------
    | OAuth registration
    |--------------------------------------------------------------------------
    |
    | Activa el registro de usuarios a través de redes sociales
    |
    */

    'oauth' => false,


    /*
    |--------------------------------------------------------------------------
    | Sign in config
    |--------------------------------------------------------------------------
    |
    | Configuración de permisos para ingresar al sistema
    |
    */

    //Permitir loguearse a los usuarios que no tienen verificado su e-mail
    'allowUnverified' => true,

    /*
    |--------------------------------------------------------------------------
    | On register actions
    |--------------------------------------------------------------------------
    |
    | Configuración de comportamiento al registrarse
    |
    */

    //Loguear al usuario inmediatamente luego de registrarse
    'autologin' => true,
);