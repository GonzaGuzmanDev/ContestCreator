<?php

/**
 * EntryMetadataFile
 *
 * @property integer $id
 * @property integer $entry_id
 * @property integer $contest_file_id
 * @property integer $entry_metadata_value_id
 * @property boolean $order
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataFile whereEntryId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataFile whereContestFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataFile whereMetadataId($value)
 * @method static \Illuminate\Database\Query\Builder|\EntryMetadataFile whereOrder($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\ContestFile $contestFile
 * @property-read \Illuminate\Database\Eloquent\Collection|\EntryMetadataValue[] $entryMetadataValue
 */
class EntryMetadataFile extends Eloquent {

	protected $fillable = [];

	public function contestFile() {
		return $this->belongsTo('ContestFile');
	}

	public function EntryMetadataValue() {
		return $this->belongsTo('EntryMetadataValue');
	}
}