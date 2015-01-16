var gulp = require('gulp'),
    sass = require('gulp-sass'),
    shell = require('gulp-shell'),
    plumber = require('gulp-plumber'),
    codeception = require('gulp-codeception');

gulp.task('default', ['publish-assets', 'sass', 'watch'])

gulp.task('sass', function()
{
    gulp.src('./public/assets/sass/main.scss')
        .pipe(plumber())
        .pipe(sass({sourceComments: 'map'}))
        .pipe(gulp.dest('./public/build/'));
});

gulp.task('publish-assets', shell.task('php artisan asset:publish-all'));

gulp.task('tests', function()
{
    gulp.src('codeception.yml')
        .pipe(plumber())
        .pipe(codeception('./vendor/bin/codecept --fail-fast', {debug: true}));
});

gulp.task('build-tests', shell.task('./vendor/bin/codecept build'));

gulp.task('watch', function ()
{
    gulp.watch([
        'public/assets/**',
        'workbench/*/public/**'
    ], ['publish-assets', 'sass']);

    gulp.watch([
        'tests/_support/*.php',
        'tests/**/*Cest.php',
        'tests/**/_bootstrap.php',
        'tests/*.yml'
    ], ['build-tests', 'tests']);
});
