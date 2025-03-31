const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const rename = require('gulp-rename');

// Caminhos
const paths = {
    styles: {
        src: 'sass/style.scss',
        dest: 'dist/css/'
    },
    scripts: {
        src: 'js/**/*.js',
        dest: 'dist/js/'
    }
};

// Compilar SASS
gulp.task('sass', function() {
    return gulp.src(paths.styles.src)
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS())
        .pipe(rename('style.min.css'))
        .pipe(gulp.dest(paths.styles.dest));
});

// Minificar e Concatenar JS
gulp.task('scripts', function() {
    return gulp.src('js/**/*.js')
        .pipe(concat('main.js'))
    return gulp.src(paths.scripts.src)
        .pipe(concat('content.js'))
        .pipe(uglify())
        .pipe(gulp.dest('js/min'))
        .pipe(rename('content.min.js'))
        .pipe(gulp.dest(paths.scripts.dest));
});

// Watch task
gulp.task('watch', function() {
    gulp.watch('sass/**/*.scss', gulp.series('sass'));
    gulp.watch('js/**/*.js', gulp.series('scripts'));
    gulp.watch(paths.scripts.src, gulp.series('scripts'));
});

gulp.task('default', gulp.series('sass', 'scripts', 'watch'));

gulp.task('build', gulp.series('sass', 'scripts'));
