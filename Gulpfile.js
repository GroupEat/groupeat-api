var gulp = require('gulp'),
    sass = require('gulp-sass'),
    exec = require('gulp-exec'),
    codeception = require('gulp-codeception');

gulp.task('sass', function()
{
    gulp.src('./public/assets/sass/main.scss')
        .pipe(sass({sourceComments: 'map'}))
        .pipe(gulp.dest('./public/build/'));
});

gulp.task('tests', function()
{
    gulp.src('codeception.yml')
        .pipe(codeception());
});
