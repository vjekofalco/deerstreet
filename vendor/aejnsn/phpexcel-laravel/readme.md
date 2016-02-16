## PHPExcel Wrapper for Laravel 4


This package was created and previously maintained by Jan Rozklad (rozklad). I am going to maintain this since rozklad took the package down. His repo is throwing a 404 on GitHub.

### Installation

1) Require package via composer.json

	"aejnsn/phpexcel-laravel": "dev-master"

2) Run composer update
	
	$ composer update

3) Open __app/config/app.php__ and add ServiceProvider to 'providers' array

	'aejnsn\PHPExcel\PHPExcelServiceProvider',

4) Optionally add to aliases

	'Excel' => 'aejnsn\PHPExcel\Facade',

### Usage

Create Excel xls file from array

	Excel::fromArray( array(
		array('1', '2', '3'),
		array('X', 'Y', 'Z')
	) )->save( base_path() . '/sample.xls' );

Create Excel xlsx file from array

	Excel::fromArray( array(
		array('Hello', 'World', '!!!'),
		array('X', 'Y', 'Z')
	) )->save( base_path() . '/sample.xlsx' );

Create array from Excel file

	Excel::excel2Array( base_path() . '/sample.xls' );

### License

[MIT license](http://opensource.org/licenses/MIT)
