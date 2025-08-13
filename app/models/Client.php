<?php

/**
 * Client
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $image
 */
class Client extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clients';

	/**
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'description', 'image'];

}