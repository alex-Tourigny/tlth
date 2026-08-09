const mix = require('laravel-mix');
const glob = require('fast-glob');
const path = require('path');
require('mix-tailwindcss');

// Build main CSS and editor CSS
mix.sass('src/scss/main.scss', 'dist/css/main.css')
   .sass('src/scss/editor.scss', 'dist/css/editor.css');

// Auto-discover and build all block styles
const blockStyles = glob.sync('src/blocks/*/style.scss');
blockStyles.forEach(stylePath => {
  const blockName = path.basename(path.dirname(stylePath));
  mix.sass(stylePath, `dist/blocks/${blockName}/style.css`);
});

// Copy all src/js → dist/js in one step.
// Do not use mix.copy() inside a loop: each call overwrote the previous CopyWebpackPlugin
// pattern in Mix 6, so only the last file (e.g. shop-btn-confetti.js) was emitted.
if (glob.sync('src/js/**/*.js').length) {
  mix.copyDirectory('src/js', 'dist/js');
}

// Auto-discover and copy block JavaScript files
const blockScripts = glob.sync('src/blocks/*/script.js');
blockScripts.forEach(scriptPath => {
  const blockName = path.basename(path.dirname(scriptPath));
  mix.copy(scriptPath, `dist/blocks/${blockName}/script.js`);
});

// Apply Tailwind CSS to all Sass files
mix.tailwind();

// Options
mix.options({
  processCssUrls: false,
  sassOptions: {
    silenceDeprecations: ['legacy-js-api']
  }
});

// Configure webpack watch options to prevent infinite loops
mix.webpackConfig({
  watchOptions: {
    ignored: [
      '**/node_modules/**',
      '**/dist/**',
      '**/.git/**',
      '**/package-lock.json',
      '**/webpack.mix.js',
      '**/mix-manifest.json',
      '**/*.map'
    ],
    aggregateTimeout: 1000,
    followSymlinks: false
  },
  ignoreWarnings: [
    {
      module: /sass-loader/,
      message: /legacy JS API/
    },
    {
      module: /sass-loader/,
      message: /The legacy JS API is deprecated/
    }
  ]
});

