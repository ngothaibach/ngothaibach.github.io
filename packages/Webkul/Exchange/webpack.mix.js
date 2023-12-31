const mix = require("laravel-mix");

if (mix == 'undefined') {
    const { mix } = require("laravel-mix");
}

require("laravel-mix-merge-manifest");

if (mix.inProduction()) {
    var publicPath = 'publishable/assets';
} else {
    var publicPath = '../../../public/themes/default/assets';
}

mix.setPublicPath(publicPath).mergeManifest();
mix.disableNotifications();

console.info("GO TO webpack.mix.js of Exchange package");

mix.js([__dirname + '/src/Resources/assets/js/app.js'], 'js/admin.js')
    .copyDirectory(__dirname + '/src/Resources/assets/images', publicPath + "/images")
    .sass(__dirname + '/src/Resources/assets/sass/admin.scss', 'css/admin.css')
    .sass(__dirname + '/src/Resources/assets/sass/default.scss', 'css/default.css')
    .sass(__dirname + '/src/Resources/assets/sass/velocity.scss', 'css/velocity.css')
    .options({
        processCssUrls: false
    });


if (! mix.inProduction()) {
    mix.sourceMaps();
}

if (mix.inProduction()) {
    mix.version();
}