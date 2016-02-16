<?php 
namespace aejnsn\PHPExcel;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

    protected static function getFacadeAccessor() { return 'phpexcel'; }

}