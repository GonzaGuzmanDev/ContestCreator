<?php

use \OAuth\OAuth2\Service\Facebook;
use \OAuth\OAuth2\Service\Google;

return array( 
	
	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session', 

	/**
	 * Consumers
	 */
	'consumers' => array(

		'Facebook' => array(
            'client_id'     => '191476330879161',
            'client_secret' => 'b6bf560a9b539f76872b75a39d481e38',
            'scope'         => array(Facebook::SCOPE_EMAIL),
        ),

        'Twitter' => array(
            'client_id'     => 'tbX66evQkflSfHDlB1XMA',
            'client_secret' => 'ctxktj5jEBdFKjsXE2U1Qx1wLCMX93nqmsAFQgK5vsQ',
            // No scope - oauth1 doesn't need scope
        ),

        'Google' => array(
            'client_id'     => '826616995034-n895stq87auhk5d7uti8vqft7113e4co.apps.googleusercontent.com',
            'client_secret' => 'QCiY2rYkUPM57q7sIz-eqEoF',
            'scope'         => array(Google::SCOPE_USERINFO_EMAIL, Google::SCOPE_USERINFO_PROFILE),
        ),

    )

);