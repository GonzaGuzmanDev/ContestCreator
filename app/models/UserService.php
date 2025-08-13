<?php

/**
 * UserService
 *
 * @property integer $id
 * @property string $service
 * @property string $service_id
 * @property integer $user_id
 * @method static \Illuminate\Database\Query\Builder|\UserService whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\UserService whereService($value) 
 * @method static \Illuminate\Database\Query\Builder|\UserService whereServiceId($value) 
 * @method static \Illuminate\Database\Query\Builder|\UserService whereUserId($value) 
 */
class UserService extends \Eloquent {

	/**
	 * Services
	 */
	const FACEBOOK = 'facebook';
	const TWITTER = 'twitter';
	const GOOGLE = 'google';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users_services';

	/**
	 *
	 * @var array
	 */
	protected $fillable = ['service','service_id','user_id'];
}