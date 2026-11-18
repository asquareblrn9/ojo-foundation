// Admin dashboard scripts. Alpine.js is bundled with Livewire 4 and started
// automatically, so register Alpine components on the `alpine:init` event.

import richEditor from './editor.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('richEditor', richEditor);

    // Gallery bulk uploader: uploads files one at a time to the component's
    // `photo` property, then asks the server to store each one.
    window.Alpine.data('bulkUploader', ($wire) => ({
        files: [],
        total: 0,
        done: 0,
        succeeded: 0,
        current: 0,
        busy: false,
        dragging: false,
        errors: [],
        get overall() {
            return this.total ? Math.round(((this.done + this.current / 100) / this.total) * 100) : 0;
        },
        queue(fileList) {
            const images = [...fileList].filter((f) => /^image\/(jpeg|png|webp)$/.test(f.type));
            [...fileList].filter((f) => !images.includes(f)).forEach((f) => this.errors.push(`${f.name}: not a JPG, PNG or WebP image.`));
            if (!images.length) return;
            if (!this.busy) {
                this.total = 0;
                this.done = 0;
                this.succeeded = 0;
            }
            this.files.push(...images);
            this.total += images.length;
            if (!this.busy) this.next();
        },
        next() {
            const file = this.files.shift();
            if (!file) {
                this.busy = false;
                $wire.uploadFinished(this.succeeded);
                return;
            }
            this.busy = true;
            this.current = 0;
            $wire.upload(
                'photo',
                file,
                async () => {
                    const result = await $wire.storePhoto();
                    if (result?.ok) this.succeeded++;
                    else this.errors.push(`${file.name}: ${result?.error ?? 'upload failed.'}`);
                    this.done++;
                    this.next();
                },
                () => {
                    this.errors.push(`${file.name}: upload failed (file may be too large).`);
                    this.done++;
                    this.next();
                },
                (e) => {
                    this.current = e.detail.progress;
                },
            );
        },
        reset() {
            this.total = 0;
            this.done = 0;
            this.succeeded = 0;
            this.errors = [];
        },
    }));

    // Toast stack: listens for Livewire `toast` events and the flashed session toast.
    window.Alpine.data('toastStack', (initial = null) => ({
        toasts: [],
        init() {
            if (initial) this.push(initial);
            this.handler = (e) => this.push(e.detail);
            window.addEventListener('toast', this.handler);
        },
        destroy() {
            window.removeEventListener('toast', this.handler);
        },
        push({ type = 'success', message }) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type, message });
            setTimeout(() => this.remove(id), 4500);
        },
        remove(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    }));
});
