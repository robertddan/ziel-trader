var gulp        = require('gulp');
var browserSync = require('browser-sync').create();
var reload      = browserSync.reload();

// Save a reference to the `reload` method

// Watch scss AND html files, doing different things with each.
gulp.task('serve', function () {
    console.log('server');
    // Serve files from the root of this project
    browserSync.init({
      watch: true,
      server: "./",
      injectChanges: true,
      serveStatic: ['.'],
      files: "./*.html",
/*
      server: {
        baseDir: "./"
      }
*/
    });
    console.log('init');
    gulp.watch("*.html").on("change", function(){browserSync.reload(); console.log('aa')});
    gulp.watch("*.html").on("change", browserSync.reload());
    //gulp.watch("*.html").on("change", reload);
    console.log('watch');
});