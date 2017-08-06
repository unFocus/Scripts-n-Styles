// Include gulp
var gulp = require('gulp');
// Include plugins
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');

var dir = 'node_modules/codemirror';
gulp.task('css', function(){
    return gulp.src([
        dir+'/lib/**/*.css',
        dir+'/theme/**/*.css',
        dir+'/mode/**/*.css',
        dir+'/addon/**/*.css'
    ])
    .pipe(concat('codemirror.min.css'))
    .pipe(cleanCSS())
    .pipe(gulp.dest('./'))
});

gulp.task('js', function() {
    return gulp.src([
        'diff_match_patch.js',
        dir+'/lib/**/*.js',
        dir+'/theme/**/*.js',
        dir+'/keymap/**/*.js',

        dir+'/addon/**/*.js',
        '!'+dir+'/addon/runmode/**/*.js',
        '!'+dir+'/addon/tern/**/*.js',
        '!'+dir+'/addon/mode/multiplex_test.js',
        '!'+dir+'/addon/mode/loadmode.js',
        dir+'/mode/**/*.js',
    ])
    .pipe(concat('codemirror.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('.'));
});

// Default Task
gulp.task('default', ['css','js']);