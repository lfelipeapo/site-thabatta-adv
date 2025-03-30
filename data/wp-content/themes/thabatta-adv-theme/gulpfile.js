const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');

// Compilar SASS
gulp.task('sass', function() {
    return gulp.src('sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS())
        .pipe(gulp.dest('css'));
});

// Minificar JS
gulp.task('scripts', function() {
    return gulp.src('js/**/*.js')
        .pipe(concat('main.js'))
        .pipe(uglify())
        .pipe(gulp.dest('js/min'));
});

// Watch task
gulp.task('watch', function() {
    gulp.watch('sass/**/*.scss', gulp.series('sass'));
    gulp.watch('js/**/*.js', gulp.series('scripts'));
});

// Default task
gulp.task('default', gulp.series('sass', 'scripts', 'watch'));