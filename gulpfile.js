var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    cache = require('gulp-cache'),
    vinylpaths = require('vinyl-paths'),
    cleancss = require('gulp-clean-css'),
    cmq = require('gulp-combine-mq'),
    uglify = require('gulp-uglify'),
    foreach = require('gulp-flatmap'),
    changed = require('gulp-changed'),
    merge = require('merge-stream'),
    del = require('del');

// Vendor JS
gulp.task('scripts', function(){
    return gulp.src([
        'bower_components/parallax.js/parallax.js'
    ])
    .pipe(foreach(function(stream, file){
        return stream
            .pipe(changed('temp/js'))
            .pipe(uglify())
            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest('temp/js'))
    }))
    .pipe(gulp.dest('public/js'))
    .pipe(notify({ message: 'Scripts task complete' }));
});

// Clean temp folder
gulp.task('clean:temp', function(){
    return gulp.src('temp/*')
    .pipe(vinylpaths(del))
});

// Default task
gulp.task('default', ['clean:temp'], function() {
    gulp.start('scripts', 'watch');
});

// Watch
gulp.task('watch', function() {
    gulp.watch(['assets/js/sources/*.js'], ['scripts']);
});
