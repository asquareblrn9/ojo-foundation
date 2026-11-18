// Public site scripts. Alpine.js is bundled with Livewire and loads on pages
// that contain Livewire components; everything here works without it.

// Gentle reveal-on-scroll for elements marked [data-reveal].
const reveal = () => {
    const items = document.querySelectorAll('[data-reveal]:not(.is-revealed)');
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduce || !('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );

    items.forEach((el) => observer.observe(el));
};

// Gallery lightbox. Items are read from [data-lightbox] buttons when opened,
// so it always matches the grid after filtering or "load more".
document.addEventListener('alpine:init', () => {
    window.Alpine.data('lightbox', () => ({
        items: [],
        index: null,
        get current() {
            return this.index === null ? null : this.items[this.index];
        },
        open(i) {
            this.items = [...this.$root.querySelectorAll('[data-lightbox]')].map((el) => ({ ...el.dataset }));
            this.index = i;
        },
        close() {
            this.index = null;
        },
        next() {
            this.index = (this.index + 1) % this.items.length;
        },
        prev() {
            this.index = (this.index - 1 + this.items.length) % this.items.length;
        },
        onKey(e) {
            if (this.index === null) return;
            if (e.key === 'Escape') this.close();
            if (e.key === 'ArrowRight') this.next();
            if (e.key === 'ArrowLeft') this.prev();
        },
    }));
});

document.documentElement.classList.add('js');
document.addEventListener('DOMContentLoaded', reveal);
// Re-run after Livewire updates the DOM (e.g. "load more").
document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morphed', () => requestAnimationFrame(reveal));
});
