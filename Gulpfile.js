var gulp = require('gulp'),
    sass = require('gulp-sass'),
    shell = require('gulp-shell'),
    notify = require('gulp-notify'),
    plumber = require('gulp-plumber'),
    codeception = require('gulp-codeception');

gulp.task('default', ['publish-assets', 'sass', 'watch'])

gulp.task('sass', function()
{
    gulp.src('./public/assets/sass/main.scss')
        .pipe(plumber())
        .pipe(sass({sourceComments: 'map'}))
        .pipe(gulp.dest('./public/build/'))
        .pipe(notify({
            message: 'Sass files compiled!',
            icon: __dirname + '/node_modules/gulp-notify/assets/gulp-error.png' // error looks better than default
        }));
});

gulp.task('publish-assets', shell.task('php artisan asset:publish-all'));

gulp.task('tests', function()
{
    gulp.src('codeception.yml')
        .pipe(plumber())
        .pipe(codeception('./vendor/bin/codecept --fail-fast', {notify: true, debug: true}))
        .on('error', notify.onError(codeceptionNotification('fail')))
        .pipe(notify(codeceptionNotification('pass')));
});

gulp.task('build-tests', shell.task('./vendor/bin/codecept build'));

gulp.task('watch', function ()
{
    gulp.watch('public/assets/**', ['publish-assets', 'sass']);
    gulp.watch([
        'tests/_support/*.php',
        'tests/**/*Cest.php',
        'tests/**/_bootstrap.php',
        'tests/*.yml'
    ], ['build-tests', 'tests']);
});

function codeceptionNotification(status)
{
    return {
        message: ( status == 'pass' ) ? 'All tests have passed!' : 'One or more tests failed...',
        icon:    __dirname + '/node_modules/gulp-codeception/assets/test-' + status + '.png'
    };
}
