<?php namespace App\Facades;
use App\Services\Image;
use Illuminate\Support\Facades\Facade;
class ImageFacade extends Facade {
    protected static function getFacadeAccessor() {
        return new Image;
    }
}