'use strict';
var imagemin = require('gulp-imagemin');

module.exports = {
    
    customImageOpt: function(){
        return imagemin([
            imagemin.jpegtran({progressive: true}),
            imagemin.optipng({optimizationLevel: 2}),
            imagemin.svgo({
                plugins: [
                    {removeViewBox: true},
                    {cleanupIDs: false}
                ]
            })
        ]);
    },
    paths: function () {
        
        return {
            src: {
                sprite: 'src/img/src/*.png',
                sprite_map: 'src/img/src_map/*.png',
                img: 'src/img/opt/*.*',
                images: 'src/images/*.*',
                fonts: 'src/fonts/*.*',
                favicons: 'src/favicons/*.*',
                user_scripts: 'src/scripts/main.js',
                app_scripts: 'src/scripts/app.js',
                style: 'src/css/main.less',
                templates: 'src/templates/'

            },
            watch: {
                img: 'src/img/opt/*.*',
                images: 'src/images/*.*',
                user_scripts: 'src/scripts/**/*.js',
                style: 'src/css/**/*.less',
                templates: 'src/templates/**/*.*'

            },
            build: {
                img: 'build/img/',
                images: 'build/images/',
                fonts: 'build/fonts/',
                favicons: 'build/favicons/',
                scripts: 'build/scripts/',
                css: 'build/css/',
            },
            dest: 'build/'
        };
    }
};