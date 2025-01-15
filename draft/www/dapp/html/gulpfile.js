var gulp = require('gulp');
var htmlreplace = require('gulp-html-replace');
 
gulp.task('default', function() {
  return gulp.src('index.html')
    .pipe(htmlreplace({
        'hello': 'Hello Timisoara'
    }))
    .pipe(gulp.dest('./'));
});