import esbuild from 'esbuild'

const isDev = process.argv.includes('--dev')

const defaultOptions = {
    define: { 'process.env.NODE_ENV': isDev ? `'development'` : `'production'` },
    bundle: true,
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    target: ['es2020'],
    minify: !isDev,
    logLevel: 'info',
}

async function build(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

await build({
    ...defaultOptions,
    entryPoints: ['resources/js/blok-field.js'],
    outfile: 'resources/dist/blok-field.js',
    format: 'iife',
})

await build({
    ...defaultOptions,
    entryPoints: ['resources/css/blok-field.css'],
    outfile: 'resources/dist/blok-field.css',
})
