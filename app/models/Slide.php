<?php

/**
 * Client
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $link
 * @property string $class
 * @property boolean $public
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Slide extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'slides';

	/**
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'description', 'image', 'link', 'class', 'public'];

	public function scopeIsPublic($query)
	{
		return $query->where('public','=','1');
	}
}