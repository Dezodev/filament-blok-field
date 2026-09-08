/**
 * Reference template for a custom block's BlockTool module - not built by
 * this package. Copy this into the host app (e.g.
 * resources/js/blok-blocks/section.js), build it with the host's own
 * bundler, and point the matching PHP BlokBlock::script() at the built
 * URL. See README.md "Declaring a custom block".
 */
export default class ExampleBlock {
    static get toolbox() {
        return { title: 'Example', icon: '<svg></svg>' }
    }

    constructor({ data, api, block }) {
        this.data = data
        this.api = api
        this.block = block
    }

    render() {
        const wrapper = document.createElement('div')
        wrapper.textContent = this.data.text ?? ''

        return wrapper
    }

    save(el) {
        return { text: el.textContent }
    }
}

// Self-register under the same `type` used by the matching PHP BlokBlock.
window.FilamentBlokFieldBlocks = window.FilamentBlokFieldBlocks ?? {}
window.FilamentBlokFieldBlocks.example = ExampleBlock
