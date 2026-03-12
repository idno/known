import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import CodeBlock from '@tiptap/extension-code-block';

export function createEditor(element, options = {}) {
    const { content = '', placeholder = 'Write something...', onChange } = options;

    const editor = new Editor({
        element,
        extensions: [
            StarterKit.configure({
                codeBlock: false,
            }),
            Link.configure({
                openOnClick: false,
                HTMLAttributes: {
                    rel: 'noopener noreferrer',
                },
            }),
            Image,
            Placeholder.configure({ placeholder }),
            CodeBlock,
        ],
        content,
        onUpdate({ editor }) {
            if (onChange) {
                onChange(editor.getHTML());
            }
        },
    });

    return editor;
}
