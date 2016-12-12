// Include gulp
var gulp = require('gulp');
// Include plugins
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

// Default Task
gulp.task('default', function() {
    return gulp.src([
        'node_modules/codemirror/lib/codemirror.js',
        'node_modules/codemirror/mode/css/css.js',
        'node_modules/codemirror/mode/coffeescript/coffeescript.js',
        'node_modules/codemirror/mode/less/less.js',
        'node_modules/codemirror/mode/javascript/javascript.js',
        'node_modules/codemirror/mode/xml/xml.js',
        'node_modules/codemirror/mode/clike/clike.js',
        'node_modules/codemirror/mode/markdown/markdown.js',
        'node_modules/codemirror/mode/gfm/gfm.js',
        'node_modules/codemirror/mode/htmlmixed/htmlmixed.js',
        'node_modules/codemirror/mode/php/php.js',
    ])
    .pipe(concat('codemirror.js'))
    .pipe(rename({suffix: '.min'}))
    .pipe(uglify())
    .pipe(gulp.dest('./'));
});