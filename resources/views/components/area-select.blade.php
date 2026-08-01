@php
    $areas = config('nhatrang_areas', []);
    $fieldName = $name ?? 'district';
    $required = $required ?? false;
    $placeholder = $placeholder ?? 'Chọn khu vực...';
    $class = $class ?? 'w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-navy focus:ring-1 focus:ring-navy';
    $submitOnChange = $submitOnChange ?? false;
    $rawSelected = $selected ?? old($fieldName, $selectedValue ?? '');

    $initialValue = '';
    $initialLabel = $placeholder;
    foreach ($areas as $area) {
        $match = ($rawSelected === ($area['value'] ?? ''))
            || in_array($rawSelected, $area['legacy'] ?? [], true);
        if ($match) {
            $initialValue = $area['value'];
            $initialLabel = $area['label'];
            break;
        }
    }

    $legacyNote = function (array $area): string {
        $legacy = $area['legacy'] ?? [];
        if ($legacy === []) {
            return '';
        }
        $shown = array_slice($legacy, 0, 4);
        $more = count($legacy) > 4 ? '…' : '';

        return '| Cũ ' . implode(', ', $shown) . $more;
    };
@endphp

<div
    class="relative"
    x-data="{
        open: false,
        value: @js($initialValue),
        label: @js($initialLabel),
        placeholder: @js($placeholder),
        submitOnChange: @js($submitOnChange),
        pick(val, lab) {
            this.value = val;
            this.label = lab || this.placeholder;
            this.open = false;
            this.$nextTick(() => {
                this.$refs.native.dispatchEvent(new Event('change', { bubbles: true }));
                if (this.submitOnChange) {
                    const form = this.$el.closest('form');
                    if (form) form.requestSubmit ? form.requestSubmit() : form.submit();
                }
            });
        }
    }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
>
    {{-- Native select: form value + HTML5 required validation --}}
    <select
        x-ref="native"
        name="{{ $fieldName }}"
        @if($required) required @endif
        class="sr-only"
        tabindex="-1"
        aria-hidden="true"
        x-model="value"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($areas as $area)
            <option value="{{ $area['value'] }}">{{ $area['label'] }}</option>
        @endforeach
    </select>

    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open.toString()"
        class="{{ $class }} flex items-center justify-between gap-2 text-left"
    >
        <span class="min-w-0 truncate" :class="value ? 'text-gray-800' : 'text-gray-400'" x-text="label"></span>
        <svg class="h-4 w-4 shrink-0 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute z-50 mt-1 max-h-72 w-full overflow-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
        role="listbox"
    >
        <button
            type="button"
            role="option"
            @click="pick('', placeholder)"
            class="block w-full px-3 py-2 text-left text-sm text-gray-400 hover:bg-gray-50"
            :class="!value && 'bg-navy/5'"
        >
            {{ $placeholder }}
        </button>

        @foreach($areas as $area)
            @php $note = $legacyNote($area); @endphp
            <button
                type="button"
                role="option"
                @click="pick(@js($area['value']), @js($area['label']))"
                class="block w-full px-3 py-2 text-left hover:bg-gray-50"
                :class="value === @js($area['value']) && 'bg-navy/5'"
            >
                <span class="block text-sm font-medium text-gray-900">{{ $area['label'] }}</span>
                @if($note !== '')
                    {{-- Chú thích tên cũ — có thể gỡ block này sau khi người dùng quen tên mới --}}
                    <span class="mt-0.5 block pl-3 text-[11px] leading-snug text-gray-400">{{ $note }}</span>
                @endif
            </button>
        @endforeach
    </div>
</div>
