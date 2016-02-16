var gulp = require('gulp');
var replace = require('gulp-replace-task');
var prompt = require('gulp-prompt');
var argv = require('yargs').argv;


gulp.task('default', function(){

	gulp.src('app/js/app.js')
		.pipe(replace({
      patterns: [
        {
          match: 'api-url',
          replacement: argv.x
        }
      ],
      usePrefix: false
    }))
	.pipe(gulp.dest('app/src/'));

})