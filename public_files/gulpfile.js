var gulp          = require('gulp'),
    sass          = require('gulp-sass'),
    addsrc        = require('gulp-add-src'),
    smartgrid     = require('smart-grid'),
    gcmq          = require('gulp-group-css-media-queries'),
    concat        = require('gulp-concat'),
    uglify        = require('gulp-uglifyjs'),
    cssnano       = require('gulp-cssnano'),
    rename        = require('gulp-rename'),
    del           = require('del'),
    cache         = require('gulp-cache'),
    autoprefixer  = require('gulp-autoprefixer');

// Таск для Sass
gulp.task('sass', async function() {
  return gulp.src('frontend/scss/**/*.scss')
    .pipe(sass({
        outputStyle: 'expanded',
        errorLogToConsole: true
      })).on('error', sass.logError)
    .pipe(autoprefixer(['last 15 versions', '> 1%', 'ie 8', 'ie 7'], { cascade: true }))
    .pipe(gcmq()) //группировка медиазапросов
    .pipe(cssnano())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('css'))
});

// настройки сетки smart-grid
gulp.task('smart-grid', (cb) => {
  smartgrid('frontend/scss/stylesheets/', {
    outputStyle: 'scss',
    filename: '_smart-grid',
    columns: 12, // number of grid columns
    offset: '1.875rem', // gutter width - 30px
    mobileFirst: false,
    mixinNames: {
        container: 'container'
    },
    container: {
      maxWidth: '1170px',
      fields: '0.9375rem' // side fields - 15px
    },
    breakPoints: {
      xs: {
          width: '20rem' // 320px
      },
      sm: {
          width: '36rem' // 576px
      },
      md: {
          width: '48rem' // 768px
      },
      lg: {
          width: '62rem' // 992px
      },
      xl: {
          width: '75rem' // 1200px
      }
    }
  });
  cb();
});

// объединям все css библиотеки в одну
gulp.task('css-lib', function(){
  return gulp.src('node_modules/normalize.css/normalize.css')
    .pipe(addsrc.append('node_modules/aos/dist/aos.css'))
    .pipe(addsrc.append('node_modules/font-awesome/css/font-awesome.css'))
    .pipe(addsrc.append('node_modules/magnific-popup/dist/magnific-popup.css'))
    .pipe(concat('libs.css'))
    .pipe(cssnano())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('css'))
});

// импортируем шрифты себе в проект:
gulp.task('font-icon', function(){
  font = [
    'node_modules/font-awesome/fonts/*.{eot,svg,ttf,woff,woff2,otf}'
  ]

  return gulp.src(font)
    .pipe(gulp.dest('fonts'));
});

// Объединяем все js либы в один файл
gulp.task('scripts', async function() {
  return gulp.src(['node_modules/jquery/dist/jquery.js'])
    .pipe(addsrc.append('node_modules/aos/dist/aos.js'))
    .pipe(addsrc.append('node_modules/magnific-popup/dist/jquery.magnific-popup.js'))
    .pipe(concat('libs.js'))
    .pipe(uglify())
    .pipe(gulp.dest('script'))
});


gulp.task('code', function() {
  return gulp.src('**/*.html')
});

gulp.task('img', function() {
  return gulp.src('frontend/img/**/*')
    .pipe(gulp.dest('img'));
});

gulp.task('font', function() {
  return gulp.src('frontend/fonts/**/*')
    .pipe(gulp.dest('fonts'));
});

gulp.task('clear-cache', function (callback) {
  return cache.clearAll();
});

// Следим за файлами
gulp.task('watch', function() {
  gulp.watch('frontend/scss/**/*.scss', gulp.parallel('sass'));
  gulp.watch('**/*.html', gulp.parallel('code'));
  gulp.watch(['frontend/js/common.js'], gulp.parallel('scripts'));
});

gulp.task('default',
     gulp.parallel('clear-cache', 'smart-grid', 'sass', 'css-lib', 'scripts', 'font-icon', 'font', 'img', 'watch'));