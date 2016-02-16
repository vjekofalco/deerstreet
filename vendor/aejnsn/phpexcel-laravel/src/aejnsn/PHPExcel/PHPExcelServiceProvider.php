<?php namespace aejnsn\PHPExcel;

use Illuminate\Support\ServiceProvider;

class PHPExcelServiceProvider extends ServiceProvider {

	protected $defer = false;

    public function boot()
    {
        $this->package('aejnsn/phpexcel-laravel');
    }

	public function register()
	{
        $this->app['phpexcel'] = $this->app->share(function($app)
        {
            return new Excel;
        });
	}

	public function provides()
	{
        return array('phpexcel');
	}

}