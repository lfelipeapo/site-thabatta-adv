const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const cleanCSS = require('gulp-clean-css');
const browsersync = require('browser-sync').create();
const rename = require('gulp-rename');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const { src, dest, watch, series, parallel } = require('gulp');

// Caminho dos arquivos
const paths = {
    styles: {
        src: 'sass/**/*.scss',
        dest: 'assets/css/'
    },
    scripts: {
        src: ['assets/js/vendor/*.js', 'assets/js/main.js'],
        dest: 'assets/js/'
    },
    images: {
        src: 'assets/images/src/**/*',
        dest: 'assets/images/'
    }
};

// Limpar diretórios - usando fs em vez de del
async function clean() {
    const fs = require('fs');
    const filesToDelete = [
        'assets/css/style.css', 
        'assets/css/style.min.css',
        'assets/js/bundle.js', 
        'assets/js/bundle.min.js'
    ];
    
    filesToDelete.forEach(file => {
        try {
            if (fs.existsSync(file)) {
                fs.unlinkSync(file);
                console.log(`Arquivo deletado: ${file}`);
            }
        } catch (err) {
            console.error(`Erro ao deletar ${file}:`, err);
        }
    });
    
    return Promise.resolve();
}

// Verificar e criar diretórios necessários
function ensureDirExists(dirPath) {
    const fs = require('fs');
    if (!fs.existsSync(dirPath)) {
        fs.mkdirSync(dirPath, { recursive: true });
        console.log(`Diretório criado: ${dirPath}`);
    }
}

// Verificar e criar diretórios
function createDirs(done) {
    ensureDirExists('sass');
    ensureDirExists('sass/components');
    ensureDirExists('assets/css');
    ensureDirExists('assets/js/vendor');
    ensureDirExists('assets/images/src');
    ensureDirExists('assets/images');
    done();
}

// Compilar SASS para CSS
function styles() {
    return src(paths.styles.src)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer()]))
        .pipe(sourcemaps.write('.'))
        .pipe(dest(paths.styles.dest))
        .pipe(browsersync.stream());
}

// Compilar SASS para CSS minificado
function stylesProd() {
    return src(paths.styles.src)
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer()]))
        .pipe(cleanCSS())
        .pipe(rename({ suffix: '.min' }))
        .pipe(dest(paths.styles.dest));
}

// Processar scripts
function scripts() {
    try {
        ensureDirExists('assets/js/vendor');
        
        // Criar arquivo vazio para evitar erro caso não exista
        const fs = require('fs');
        const vendorDir = 'assets/js/vendor';
        const placeholderFile = `${vendorDir}/.placeholder`;
        
        if (!fs.readdirSync(vendorDir).length) {
            fs.writeFileSync(placeholderFile, '// Placeholder para diretório vendor');
            console.log('Arquivo placeholder criado para evitar erro de diretório vazio');
        }
        
        return src([
                'assets/js/vendor/**/*.js',
                'assets/js/main.js'
            ])
            .pipe(sourcemaps.init())
            .pipe(babel({
                presets: ['@babel/preset-env']
            }))
            .pipe(concat('bundle.js'))
            .pipe(sourcemaps.write('.'))
            .pipe(dest(paths.scripts.dest))
            .pipe(browsersync.stream());
    } catch (err) {
        console.error('Erro na tarefa de scripts:', err);
        return Promise.resolve('Tarefa de scripts finalizada com erro');
    }
}

// Processar scripts para produção
function scriptsProd() {
    return src(paths.scripts.src)
        .pipe(babel({
            presets: ['@babel/preset-env']
        }))
        .pipe(concat('bundle.min.js'))
        .pipe(uglify())
        .pipe(dest(paths.scripts.dest));
}

// Otimizar imagens
function images() {
    try {
        // Verificar se o diretório existe
        const fs = require('fs');
        if (!fs.existsSync(paths.images.src)) {
            fs.mkdirSync(paths.images.src, { recursive: true });
            console.log(`Diretório criado: ${paths.images.src}`);
        }
        
        return src(paths.images.src)
            .pipe(imagemin())
            .pipe(dest(paths.images.dest))
            .pipe(browsersync.stream());
    } catch (err) {
        console.error('Erro na tarefa de imagens:', err);
        return Promise.resolve('Tarefa de imagens finalizada com erro');
    }
}

// Iniciar BrowserSync
function serve(done) {
    browsersync.init({
        proxy: 'localhost/site-thabatta-adv',
        open: false,
        notify: false
    });
    done();
}

// Recarregar BrowserSync
function browserSyncReload(done) {
    browsersync.reload();
    done();
}

// Monitorar arquivos
function watchFiles() {
    watch(paths.styles.src, styles);
    watch(paths.scripts.src, scripts);
    watch(paths.images.src, images);
    watch(['**/*.php'], browserSyncReload);
}

// Definir tarefas complexas
const js = series(scripts, scriptsProd);
const css = series(styles, stylesProd);
const build = series(clean, createDirs, parallel(css, js, images));
const dev = series(clean, createDirs, styles, scripts, serve, watchFiles);

// Exportar tarefas
exports.clean = clean;
exports.styles = styles;
exports.stylesProd = stylesProd;
exports.scripts = scripts;
exports.scriptsProd = scriptsProd;
exports.images = images;
exports.build = build;
exports.watch = series(styles, scripts, serve, watchFiles);
exports.default = dev;
