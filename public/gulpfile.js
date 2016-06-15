/**
 * @file
 * This file contains gulp-based tools for interacting with SASS.
 */

(function () {
  'use strict';

  var gulp = require('gulp'),
      sass = require('gulp-sass'),
      autoprefixer = require('gulp-autoprefixer'),
      cleanCSS = require('gulp-clean-css');

  /**
   * @task sass
   * Compiles sass files within this theme.
   */
  gulp.task('sass', function () {
    gulp.src('./sass/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer('last 2 version'))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('./css/'));

  });

  /**
   * @task sass:watch
   * Watches for changes to sass files and recompiles css when changes are made.
   */
  gulp.task('sass:watch', function () {
    gulp.watch('./sass/**/*.scss', ['sass']);
  });

  /**
   * @task sass:default
   * Take care of default tasks by just typing gulp.
   */
  gulp.task('default', ['sass:watch']);
})();
