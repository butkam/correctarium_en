var gulp        = require('gulp');
var postcss     = require('gulp-postcss');
var atImport    = require("postcss-import");
var simpleVars  = require("postcss-simple-vars");
var colorAlpha  = require("postcss-color-alpha");
var cssnext     = require('postcss-cssnext');
var nested      = require('postcss-nested');
var mqpacker    = require('css-mqpacker');
var nano        = require('gulp-cssnano');
var rename      = require('gulp-rename');

var processors = [
  atImport,
  cssnext({
    browsers: ['last 2 versions'],
    features: {
      nesting: false
    }
  }),
  simpleVars,
  nested,
  colorAlpha,
  mqpacker
];

gulp.task('css', function () {
  return gulp.src('./css/style.css')
    .pipe(postcss(processors))
    .pipe(nano())
    .pipe(rename({
      extname: '.min.css'
    }))
    .pipe(gulp.dest('./css/main'));
});

gulp.task('watch', function(){
  gulp.watch('./css/**/*.css', ['css']);
});

gulp.task('default', ['css', 'watch']);
