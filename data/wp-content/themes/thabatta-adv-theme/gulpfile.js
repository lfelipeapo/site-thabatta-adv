const gulp = require('gulp');
const sass = require('sass');
const gulpSass = require('gulp-sass')(sass);
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cleanCSS = require('gulp-clean-css');
const plumber = require('gulp-plumber');
const sourcemaps = require('gulp-sourcemaps');
const browserSync = require('browser-sync').create();
const rename = require('gulp-rename');

async function clean() {
  const { deleteSync } = await import('del');
  return deleteSync(['./assets/css/*']);
}

const paths = {
  styles: {
    src: './sass/**/*.scss',
    dest: './assets/css'
  }
};

function styles() {
  return gulp.src(paths.styles.src)
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(gulpSass({
      outputStyle: 'expanded',
      includePaths: ['node_modules']
    }).on('error', gulpSass.logError))
    .pipe(postcss([autoprefixer()]))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.styles.dest))
    .pipe(browserSync.stream());
}

function stylesProd() {
  return gulp.src(paths.styles.src)
    .pipe(plumber())
    .pipe(gulpSass({
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    }).on('error', gulpSass.logError))
    .pipe(postcss([autoprefixer()]))
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.styles.dest));
}

function watchFiles() {
  browserSync.init({
    proxy: 'https://localhost/',
    notify: false
  });

  gulp.watch(paths.styles.src, styles);
  gulp.watch('./**/*.php').on('change', browserSync.reload);
}

// Exportações
exports.clean = clean;
exports.styles = styles;
exports.stylesProd = stylesProd;
exports.build = gulp.series(clean, gulp.parallel(styles, stylesProd));
exports.watch = gulp.series(styles, watchFiles);
exports.default = gulp.series(clean, styles, watchFiles);
