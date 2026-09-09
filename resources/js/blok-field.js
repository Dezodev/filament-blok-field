import { Blok } from '@bloklabs/core'

/**
 * Custom block modules self-register their BlockTool class here on load
 * (see resources/js/blocks/example.js), so the field never has to know
 * the list of custom blocks itself - it only reads what got registered.
 */
window.FilamentBlokFieldBlocks = window.FilamentBlokFieldBlocks ?? {}

document.addEventListener('alpine:init', () => {
    Alpine.data('blokEditor', ({ state }) => ({
        editor: null,
        state,

        init() {
            this.editor = new Blok({
                holder: this.$el,
                data: this.state ?? { blocks: [] },
                tools: { ...window.FilamentBlokFieldBlocks },
                onSave: (data) => {
                    this.state = data
                },
            })

            this.$el.addEventListener('livewire:navigating', () => this.destroy(), { once: true })
        },

        destroy() {
            this.editor?.destroy?.()
        },
    }))
})
