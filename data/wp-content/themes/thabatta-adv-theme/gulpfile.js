const gulp = require('gulp');
const sass = require('sass');
const gulpSass = require('gulp-sass')(sass);
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const plumber = require('gulp-plumber');
const imagemin = require('gulp-imagemin');
const rename = require('gulp-rename');
const del = require('del');
const browserSync = require('browser-sync').create();
const eslint = require('gulp-eslint');

// Caminhos
const paths = {
  styles: {
    src: 'src/scss/**/*.scss',
    dest: 'assets/css/'
  },
  scripts: {
    src: 'src/js/**/*.js',
    dest: 'assets/js/'
  },
  images: {
    src: 'src/images/**/*',
    dest: 'assets/images/'
  },
  php: {
    src: '**/*.php'
  }
};

// Limpar diretórios de destino
function clean() {
  return del([
    paths.styles.dest,
    paths.scripts.dest,
    paths.images.dest
  ]);
}

// Processar estilos
function styles() {
  return gulp.src(paths.styles.src)
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(gulpSass({
      outputStyle: 'expanded',
      includePaths: ['node_modules']
    }).on('error', gulpSass.logError))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.styles.dest))
    .pipe(browserSync.stream());
}

// Minificar estilos para produção
function stylesProd() {
  return gulp.src(paths.styles.src)
    .pipe(plumber())
    .pipe(gulpSass({
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    }).on('error', gulpSass.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.styles.dest));
}

// Processar scripts
function scripts() {
  return gulp.src(paths.scripts.src)
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(babel({
      presets: ['@babel/preset-env']
    }))
    .pipe(concat('main.js'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.scripts.dest))
    .pipe(browserSync.stream());
}

// Minificar scripts para produção
function scriptsProd() {
  return gulp.src(paths.scripts.src)
    .pipe(plumber())
    .pipe(babel({
      presets: ['@babel/preset-env']
    }))
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.scripts.dest));
}

// Otimizar imagens
function images() {
  return gulp.src(paths.images.src)
    .pipe(plumber())
    .pipe(imagemin([
      imagemin.gifsicle({ interlaced: true }),
      imagemin.mozjpeg({ quality: 80, progressive: true }),
      imagemin.optipng({ optimizationLevel: 5 }),
      imagemin.svgo({
        plugins: [
          { removeViewBox: false },
          { cleanupIDs: false }
        ]
      })
    ]))
    .pipe(gulp.dest(paths.images.dest))
    .pipe(browserSync.stream());
}

// Verificar qualidade do código JavaScript
function lint() {
  return gulp.src(paths.scripts.src)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError());
}

// Observar alterações nos arquivos
function watch() {
  browserSync.init({
    proxy: 'localhost/site-thabatta-adv',
    open: false
  });

  gulp.watch(paths.styles.src, styles);
  gulp.watch(paths.scripts.src, gulp.series(lint, scripts));
  gulp.watch(paths.images.src, images);
  gulp.watch(paths.php.src).on('change', browserSync.reload);
}

// Gerar componentes web
function generateComponents(cb) {
  const { exec } = require('child_process');
  exec('php scripts/generate-web-components.php', (err, stdout, stderr) => {
    console.log(stdout);
    if (err) {
      console.error(stderr);
    }
    cb();
  });
}

// Gerar scripts de administração
function generateAdminJs(cb) {
  const { exec } = require('child_process');
  exec('php scripts/generate-admin-js.php', (err, stdout, stderr) => {
    console.log(stdout);
    if (err) {
      console.error(stderr);
    }
    cb();
  });
}

// Gerar estilos de administração
function generateAdminCss(cb) {
  const { exec } = require('child_process');
  exec('php scripts/generate-admin-css.php', (err, stdout, stderr) => {
    console.log(stdout);
    if (err) {
      console.error(stderr);
    }
    cb();
  });
}

// Gerar arquivos SCSS
function generateScss(cb) {
  const { exec } = require('child_process');
  exec('php scripts/generate-scss.php', (err, stdout, stderr) => {
    console.log(stdout);
    if (err) {
      console.error(stderr);
    }
    cb();
  });
}

// Tarefas de desenvolvimento
const dev = gulp.series(
  clean,
  gulp.parallel(styles, gulp.series(lint, scripts), images)
);

// Tarefas de produção
const build = gulp.series(
  clean,
  generateComponents,
  generateAdminJs,
  generateAdminCss,
  generateScss,
  gulp.parallel(stylesProd, scriptsProd, images)
);

// Tarefas padrão
exports.clean = clean;
exports.styles = styles;
exports.scripts = scripts;
exports.images = images;
exports.lint = lint;
exports.watch = watch;
exports.generateComponents = generateComponents;
exports.generateAdminJs = generateAdminJs;
exports.generateAdminCss = generateAdminCss;
exports.generateScss = generateScss;
exports.build = build;
exports.default = gulp.series(dev, watch);
