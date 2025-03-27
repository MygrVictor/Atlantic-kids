const Encore = require('@symfony/webpack-encore'); // S'assurer que cette ligne est bien présente

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-transform-class-properties');
    })
    .enablePostCssLoader()
    .enableSingleRuntimeChunk() 
