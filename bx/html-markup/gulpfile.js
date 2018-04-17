var hepler = require("./gulpHepler");

var gulp = require("gulp");
var less = require("gulp-less");
var connect = require("gulp-connect");
var notifier = require('node-notifier');
var notify = require("gulp-notify");
var spritesmith = require('gulp.spritesmith');
var buffer = require('vinyl-buffer');
var imagemin = require('gulp-imagemin');
var merge = require('merge-stream');
var nunjucksRender = require('gulp-nunjucks-render');
var rigger = require('gulp-rigger');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');
var clean = require('gulp-clean');
var watch = require('gulp-watch');
var uncache = require('gulp-uncache');
var mjml = require('gulp-mjml');
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var postcssSVG = require('postcss-svg');
var gulpsync = require('gulp-sync')(gulp);
var path = hepler.paths();

// Server
gulp.task('connect', function () {
    return connect.server({
        port: 1378,
        livereload: true,
        root: 'build'
    });
});

gulp.task('sprite', function () {

    var spriteData = gulp.src(path.src.sprite).pipe(spritesmith({
        imgName: '../img/sprite.png',
        cssName: 'sprite.less',
        padding: 20
    }));

    // Pipe image stream through image optimizer and onto disk
    var imgStream = spriteData.img
    // DEV: We must buffer our stream into a Buffer for `imagemin`
        .pipe(buffer())
        .pipe(hepler.customImageOpt())
        .pipe(gulp.dest(path.build.img));

    // Pipe CSS stream through CSS optimizer and onto disk
    var cssStream = spriteData.css
        .pipe(gulp.dest('src/css/blocks/'));

    // Return a merged stream to handle both `end` events
    return merge(imgStream, cssStream);
});

gulp.task('sprite_map', function () {
    var spriteData = gulp.src(path.src.sprite_map).pipe(spritesmith({
        imgName: '../img/sprite_map.png',
        cssName: 'sprite_map.less',
        padding: 20
    }));

    // Pipe image stream through image optimizer and onto disk
    var imgStream = spriteData.img
    // DEV: We must buffer our stream into a Buffer for `imagemin`
        .pipe(buffer())
        .pipe(hepler.customImageOpt())
        .pipe(gulp.dest(path.build.img));

    // Pipe CSS stream through CSS optimizer and onto disk
    var cssStream = spriteData.css
        .pipe(gulp.dest('src/css/blocks/'));

    // Return a merged stream to handle both `end` events
    return merge(imgStream, cssStream);
});

// User script first run
gulp.task('script-init', function () {
    gulp.src(path.src.user_scripts)
        .pipe(rigger())
        .pipe(uglify({comments: false}))
        .pipe(rename('site.js'))
        .pipe(gulp.dest(path.build.scripts))
        .pipe(connect.reload());

    return gulp.src('src/scripts/contest-landing/site.js')
        .pipe(uglify({comments: false}))
        .pipe(gulp.dest('build/scripts/contest-landing'));
});

// Js
gulp.task('js', function () {
    gulp.src(path.src.app_scripts)
        .pipe(rigger())
        .on('error', function (err) {
            notifier.notify({
                'title': "JS Error",
                'message': err.message
            });
            return false;
        })
        .pipe(uglify({comments: false}))
        .pipe(rename('app.min.js'))
     
        .pipe(gulp.dest(path.build.scripts))
        .pipe(connect.reload());

    return gulp.src('src/scripts/contest-landing/app.js')
        .pipe(rigger())
        .on('error', function (err) {
            notifier.notify({
                'title': "Error",
                'message': err.message
            });
            return false;
        })
        .pipe(uglify({comments: false}))
        .pipe(rename('app.min.js'))
        /* .pipe(notify({
         'title': 'JS compilation',
         'message': 'JS compilation is fine!!'
         }))*/
        .pipe(gulp.dest(path.dest + 'scripts/contest-landing'))
        .pipe(connect.reload());
});

// Less
gulp.task('less', function () {
    return gulp.src([path.src.style, 'src/css/contest-landing.less'])
        .pipe(less({compress: true}))
        .on('error', function (err) {
            notifier.notify({
                'title': 'LESS Error',
                'message': err.message
            });
            console.log(err.message);
            return false;
        })
        .pipe(postcss([
            autoprefixer({browsers: ['last 2 versions']}),
            postcssSVG({paths: ['src/img/svg-icons/'],}),
        ]))
		/* .pipe(notify({
		 'title': 'LESS compilation',
		 'message': 'LESS compilation is fine!!'
		 }))*/
        .pipe(gulp.dest(path.build.css))
        .pipe(connect.reload());
});

// Html
gulp.task('nunjucks', function () {
    // Gets .html and .nunjucks files in pages
    return gulp.src(path.src.templates + '*.+(twig)')
        .pipe(nunjucksRender({
            path: [path.src.templates]
        }))
        .on('error', function (err) {
            notifier.notify({
                'title': "TWIG Error",
                'message': err.message
            });
            console.log(err.message);
            return false;
        })
        .pipe(uncache())
        .pipe(gulp.dest(path.dest))
        .pipe(connect.reload());
});

// Images first run
gulp.task('images-init', function () {
    // Copy images
    return imgStream = gulp.src(path.src.img)
        .pipe(hepler.customImageOpt())
        .pipe(gulp.dest(path.build.img))
        .pipe(connect.reload());
});

// User images watch
gulp.task('user-images', function () {
    return imgStream = gulp.src(path.src.images)
        //.pipe(hepler.customImageOpt())
        .pipe(gulp.dest(path.build.images))
        .pipe(connect.reload());
});

// Fonts first run
gulp.task('fonts-init', function () {
    // Copy fonts
    return gulp.src(path.src.fonts)
        .pipe(gulp.dest(path.build.fonts));
});

// Favicons first run
gulp.task('favicons-init', function () {
    // Copy favicons
    return gulp.src(path.src.favicons)
        .pipe(gulp.dest(path.dest));
});

// Mail
gulp.task('mjml', function () {
    return gulp.src(path.src + 'mailtemplates/*.htm')
        .pipe(mjml())
        .pipe(gulp.dest(path.destination + 'mailtemplates/'))
        .pipe(connect.reload());
});

// Cleaner that runs when launch gulp
gulp.task('clean', function () {
    return gulp.src(path.dest, {read: false})
        .pipe(clean());
});

// Watch for less, js and twig files, images
gulp.task('watch', function () {

    gulp.watch(path.watch.img, function(event, cb) {
        gulp.start('images-init');
    });
    gulp.watch(path.watch.images, function(event, cb) {
        gulp.start('user-images');
    })
    gulp.watch(path.watch.templates, function(event, cb) {
        gulp.start('nunjucks');
    })
    gulp.watch(path.watch.style, function(event, cb) {
        gulp.start('less');
    })
    gulp.watch(path.watch.user_scripts, function(event, cb) {
        gulp.start('script-init');
    })

});

// Default task compiles all and starts server, then watches
gulp.task('default', gulpsync.sync([
    'clean',
    [
        'sprite',
        'sprite_map',
        [
            'less',
            'nunjucks',
            'js',
            // 'mjml',
            'images-init',
            'user-images',
            'script-init',
            'fonts-init',
            'favicons-init',
            'connect',
            'watch',
        ]
    ]
]));