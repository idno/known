import Alpine from 'alpinejs';
import { createEditor } from './editor-core.js';

// Rename the current editor.js to editor-core.js, then this file registers the Alpine component:
Alpine.data('tiptapEditor', (uniqueId) => ({
    editor: null,

    init() {
        const container = document.getElementById(`${uniqueId}-editor`);
        const textarea = document.getElementById(uniqueId);

        this.editor = createEditor(container, {
            content: textarea.value,
            onChange: (html) => {
                textarea.value = html;
            },
        });
    },

    toggleLink() {
        if (this.editor.isActive('link')) {
            this.editor.chain().focus().unsetLink().run();
        } else {
            const url = prompt('Enter URL');
            if (url) {
                this.editor.chain().focus().setLink({ href: url }).run();
            }
        }
    },

    addImage() {
        const url = prompt('Enter image URL');
        if (url) {
            this.editor.chain().focus().setImage({ src: url }).run();
        }
    },

    setHeading(value) {
        if (value === 'paragraph') {
            this.editor.chain().focus().setParagraph().run();
        } else {
            this.editor.chain().focus().toggleHeading({ level: parseInt(value) }).run();
        }
    },
}));
