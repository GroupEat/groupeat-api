var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('sass', function()
{
    gulp.src('./public/assets/sass/main.scss')
        .pipe(sass({sourceComments: 'map'}))
        .pipe(gulp.dest('./public/build/'));
});
