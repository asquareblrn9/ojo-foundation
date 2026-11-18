// Rich-text editor for the admin (Tiptap 3), wrapped as an Alpine component.
// Usage (see components/admin/rich-editor.blade.php):
//   x-data="richEditor($wire.entangle('body'), 'Placeholder…')"
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';

export default (content, placeholder = 'Start writing…') => {
    // Keep the editor outside Alpine's reactive proxy (proxies break ProseMirror).
    let editor = null;

    return {
        content,
        tick: 0,
        uploading: false,
        uploadProgress: 0,
        error: null,

        init() {
            editor = new Editor({
                element: this.$refs.editor,
                content: this.content || '',
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [2, 3, 4] },
                        code: false,
                        codeBlock: false,
                        link: { openOnClick: false, autolink: true, defaultProtocol: 'https', protocols: ['mailto', 'tel'] },
                    }),
                    Image.configure({ inline: false }),
                    Placeholder.configure({ placeholder }),
                ],
                editorProps: {
                    attributes: { class: 'prose prose-slate max-w-none min-h-[18rem] px-4 py-3 focus:outline-none' },
                },
                onUpdate: ({ editor }) => {
                    this.content = editor.isEmpty ? '' : editor.getHTML();
                },
                onTransaction: () => {
                    this.tick++;
                },
            });

            // Reflect external changes (e.g. form reset) into the editor.
            this.$watch('content', (value) => {
                if (editor && value !== editor.getHTML() && !(editor.isEmpty && !value)) {
                    editor.commands.setContent(value || '', { emitUpdate: false });
                }
            });
        },

        destroy() {
            editor?.destroy();
            editor = null;
        },

        isActive(type, attrs = {}) {
            this.tick; // dependency so the toolbar re-renders on every transaction
            return editor ? editor.isActive(type, attrs) : false;
        },

        run(command, ...args) {
            editor?.chain().focus()[command](...args).run();
        },

        setLink() {
            const previous = editor.getAttributes('link').href || '';
            const url = window.prompt('Link URL (leave empty to remove the link)', previous);
            if (url === null) return;
            if (url.trim() === '') {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();
                return;
            }
            editor.chain().focus().extendMarkRange('link').setLink({ href: url.trim() }).run();
        },

        pickImage() {
            this.$refs.file.click();
        },

        uploadImage(event) {
            const file = event.target.files[0];
            event.target.value = '';
            if (!file) return;

            this.error = null;
            this.uploading = true;
            this.uploadProgress = 0;

            this.$wire.upload(
                'editorUpload',
                file,
                async () => {
                    try {
                        const url = await this.$wire.storeEditorUpload();
                        if (url) {
                            const alt = window.prompt('Describe this image for screen readers (alt text)', '') || '';
                            editor.chain().focus().setImage({ src: url, alt }).run();
                        }
                    } catch (e) {
                        this.error = 'The image could not be uploaded. Use a JPG, PNG or WebP under 5MB.';
                    }
                    this.uploading = false;
                },
                () => {
                    this.uploading = false;
                    this.error = 'The image could not be uploaded. Use a JPG, PNG or WebP under 5MB.';
                },
                (e) => {
                    this.uploadProgress = e.detail.progress;
                },
            );
        },
    };
};
