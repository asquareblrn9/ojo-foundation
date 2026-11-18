<div>
    <x-admin.page-header title="Settings" description="Foundation details, branding and website behaviour." />

    <div class="grid gap-6 lg:grid-cols-[14rem_1fr]">
        <nav class="-mx-1 flex gap-1 overflow-x-auto px-1 lg:mx-0 lg:flex-col lg:px-0" aria-label="Settings sections">
            @foreach ($tabs as $key => [$label, $icon])
                <button type="button" wire:click="$set('tab', '{{ $key }}')" @class([
                    'flex shrink-0 items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium transition',
                    'bg-white text-navy-900 shadow-sm ring-1 ring-slate-200' => $tab === $key,
                    'text-slate-600 hover:bg-white/70 hover:text-navy-900' => $tab !== $key,
                ])>
                    {{ svg('lucide-'.$icon, ['class' => 'size-4 '.($tab === $key ? 'text-gold-600' : 'text-slate-400')]) }} {{ $label }}
                </button>
            @endforeach
        </nav>

        <form wire:submit="save" class="card" novalidate>
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-navy-900">{{ $tabs[$tab][0] }}</h3>
            </div>
            <div class="grid gap-6 p-6 md:grid-cols-2">
                @foreach ($fields as $field)
                    @php $wide = in_array($field['format'], ['textarea', 'image', 'url', 'bool'], true); @endphp
                    <div wire:key="setting-{{ $field['key'] }}" @class(['md:col-span-2' => $wide])>
                        @switch($field['format'])
                            @case('image')
                                <x-admin.image-upload model="uploads.{{ $field['key'] }}" :upload="$uploads[$field['key']] ?? null" :current="$field['current']"
                                    :label="$field['label']" :help="$field['help']" remove="removeImage('{{ $field['key'] }}')" aspect="aspect-[3/1]" class="max-w-lg [&_img]:object-contain" />
                                @break
                            @case('bool')
                                <x-ui.toggle :label="$field['label']" :help="$field['help']" model="values.{{ $field['key'] }}" class="rounded-xl border border-slate-200 p-4" />
                                @break
                            @case('textarea')
                                <x-ui.textarea :label="$field['label']" :help="$field['help']" model="values.{{ $field['key'] }}" rows="3" />
                                @break
                            @default
                                <x-ui.input :label="$field['label']" :help="$field['help']" model="values.{{ $field['key'] }}"
                                    :type="match ($field['format']) { 'email' => 'email', 'url' => 'url', 'tel' => 'tel', default => 'text' }"
                                    :required="$field['key'] === 'site_name'" />
                        @endswitch
                    </div>
                @endforeach
            </div>

            @if ($tab === 'notifications')
                <div class="mx-6 mb-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                    Emails are sent with the mail server configured on the hosting server (<code>MAIL_*</code> settings) and are queued, so a queue worker must be running.
                </div>
            @endif

            <div class="flex justify-end border-t border-slate-100 px-6 py-4">
                <x-ui.button target="save">Save settings</x-ui.button>
            </div>
        </form>
    </div>
</div>
