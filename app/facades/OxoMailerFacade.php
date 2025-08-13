<?php namespace App\Facades;
use App\Services\OxoMailer;
use Illuminate\Support\Facades\Facade;


class OxoMailerFacade extends Facade {
    protected static function getFacadeAccessor() {
        return new OxoMailer;
    }
}