<?php

/**
 * TicketCheck
 *
 * @property integer $id
 * @property integer $ticket_id
 */
class TicketCheck extends Eloquent {

	protected $fillable = ['ticket_id'];
}