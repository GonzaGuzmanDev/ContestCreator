<?php

/**
 * EntryMetadataValue
 *
 * @property integer $id
 * @property integer $entry_id
 * @property integer $entry_metadata_field_id
 * @property string $value
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataValue whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataValue whereEntryId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataValue whereEntryMetadataFieldId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataValue whereValue($value)
 * @property-read EntryMetadataField $FilesEntryMetadataValues
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataFile[] $EntryMetadataFiles
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataFile[] $files
 */
class EntryMetadataValue extends Eloquent {

	protected $fillable = [];
	protected $hidden = ['created_at','updated_at'];

	public function EntryMetadataField() {
		return $this->belongsTo('EntryMetadataField');
	}

	public function entry() {
		return $this->belongsTo('Entry');
	}

	public function EntryMetadataFiles() {
		return $this->hasMany('EntryMetadataFile');
	}

	public function files() {
		/*return $this->belongsToMany('ContestFile', 'entry_metadata_files')->with(['ContestFileVersions' => function ($query) {
			$query->select(['contest_file_versions.id','format_id','contest_file_id','size','sizes','duration','contest_file_versions.extension','source','status','percentage'])
			//DB::raw('file force index(id)')
            ->join('formats as fo', 'fo.id', '=', 'format_id')
            ->orderBy('position', 'asc');
		}]);*/
		/* $$ REVISAR QUERY */
		return $this->belongsToMany('ContestFile', 'entry_metadata_files')->with(['ContestFileVersions' => function ($query) {
			$query->distinct()->select(['contest_file_versions.id','format_id','contest_file_id','size','sizes','duration','contest_file_versions.extension','source','status','percentage','storage_bucket'])
			->join('formats as fo', function($join){
				$join->on('fo.id', '=', 'contest_file_versions.format_id');
				$join->orOn(DB::raw("contest_file_versions.extension NOT IN ('".implode("','", ContestFileVersion::NOT_DOWNLOADABLE)."')"), DB::raw(''), DB::raw(''));
			})
			->orderBy('position', 'asc');
	    }]);
	}

	public function simpleFiles() {
		return $this->belongsToMany('ContestFile', 'entry_metadata_files');//->select(['code','name']);
	}
}