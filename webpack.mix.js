const mix = require('laravel-mix');

// website js
var scripts = [];
scripts.push('resources/assets/bootstrap/bootstrap.bundle.min.js');
scripts.push('resources/assets/jquery/jquery.min.js');
mix.js(scripts, 'public/website/app.js');

// website css
var styles = [];
styles.push('resources/assets/bootstrap/bootstrap.min.css');
mix.styles(styles, 'public/website/app.css');