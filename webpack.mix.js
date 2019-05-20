const mix = require('laravel-mix');

// website css
var styles = [];
styles.push('resources/assets/website/bootstrap.min.css');
mix.styles(styles, 'public/assets/website/app.css');

// website js
var scripts = [];
scripts.push('resources/assets/website/jquery.min.js');
scripts.push('resources/assets/website/bootstrap.bundle.min.js');
mix.js(scripts, 'public/assets/website/app.js');

// // admin css
// var styles = [];
// styles.push('resources/assets/admin/bootstrap.min.css');
// styles.push('resources/assets/admin/font-awesome.min.css');
// styles.push('resources/assets/admin/select2.min.css');
// styles.push('resources/assets/admin/adminlte.min.css');
// styles.push('resources/assets/admin/skin-blue.min.css');
// mix.styles(styles, 'public/assets/admin/app.css');

// // admin js
// var scripts = [];
// scripts.push('resources/assets/admin/jquery.min.js');
// scripts.push('resources/assets/admin/bootstrap.min.js');
// scripts.push('resources/assets/admin/select2.full.min.js');
// scripts.push('resources/assets/admin/adminlte.min.js');
// mix.js(scripts, 'public/assets/admin/app.js');