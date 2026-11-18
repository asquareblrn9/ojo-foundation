<?php

namespace App\Support\Concerns;

use App\Services\ImageService;
use App\Support\Media;
use Livewire\Attributes\Renderless;

/**
 * Image uploads from inside the rich-text editor. The editor uploads the
 * file to $editorUpload with $wire.upload(), then calls storeEditorUpload()
 * and inserts the returned URL. Requires Livewire\WithFileUploads.
 */
trait WithEditorUploads
{
    public $editorUpload;

    #[Renderless]
    public function storeEditorUpload(ImageService $images): ?string
    {
        $this->validate([
            'editorUpload' => ['required', 'image', 'mimes:'.implode(',', config('foundation.uploads.image_mimes')), 'max:'.config('foundation.uploads.image_max_kb')],
        ]);

        $path = $images->storeSingle($this->editorUpload, 'content', 1600);
        $this->reset('editorUpload');

        return Media::url($path);
    }
}
