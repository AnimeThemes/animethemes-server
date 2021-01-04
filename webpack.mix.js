let mix = require('laravel-mix');

mix.copy('node_modules/jquery/dist/jquery.min.js', 'public/js/vendor/jquery.min.js');

var modernizr = require("modernizr");

fs = require('fs');

modernizr.build({
    "options": [
        "setClasses"
    ],
    "feature-detects": [
        "video"
    ]
}, function (result) {
    fs.writeFile('public/js/vendor/modernizr.min.js', result, function (err, data) {
        if (err) {
            return console.log(err);
        }
    });
});
