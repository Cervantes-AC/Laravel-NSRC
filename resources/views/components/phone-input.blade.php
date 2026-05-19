@props([
    'name' => 'phone',
    'id' => null,
    'value' => '',
    'label' => 'Phone Number',
    'required' => false,
    'disabled' => false,
    'placeholder' => '900 000 0000',
    'countryCode' => '63',
])

<div x-data="phoneInput('{{ $countryCode }}', '{{ $value }}')" {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif
    <div class="flex rounded-lg overflow-hidden border border-slate-300 dark:border-slate-600 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition">
        <select x-model="selectedCountry" @change="updateCode()" class="w-24 border-0 border-r border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm text-slate-700 dark:text-slate-200 py-2.5 px-2 focus:ring-0">
            <template x-for="c in countries" :key="c.code">
                <option :value="c.code" x-text="c.flag + ' +' + c.code"></option>
            </template>
        </select>
        <input type="tel"
            id="{{ $id ?? $name }}"
            name="{{ $name }}"
            x-model="localValue"
            @input="formatPhone()"
            :placeholder="placeholder"
            value="{{ $value }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            class="flex-1 border-0 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 bg-white dark:bg-slate-800 placeholder-slate-400 focus:ring-0" />
    </div>
    <input type="hidden" name="{{ $name }}_full" x-model="fullNumber" />
    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('phoneInput', (defaultCode, initialValue) => ({
        countries: [
            { code: '63', flag: '🇵🇭' },
            { code: '1', flag: '🇺🇸' },
            { code: '44', flag: '🇬🇧' },
            { code: '81', flag: '🇯🇵' },
            { code: '86', flag: '🇨🇳' },
            { code: '91', flag: '🇮🇳' },
            { code: '62', flag: '🇮🇩' },
            { code: '65', flag: '🇸🇬' },
            { code: '60', flag: '🇲🇾' },
            { code: '82', flag: '🇰🇷' },
            { code: '64', flag: '🇳🇿' },
            { code: '61', flag: '🇦🇺' },
            { code: '49', flag: '🇩🇪' },
            { code: '33', flag: '🇫🇷' },
        ],
        selectedCountry: defaultCode,
        localValue: initialValue || '',
        placeholder: '900 000 0000',

        init() {
            this.updatePlaceholder();
            if (this.localValue) this.formatPhone();
        },

        updateCode() {
            this.updatePlaceholder();
        },

        updatePlaceholder() {
            const masks = {
                '63': '900 000 0000',
                '1': '(000) 000-0000',
                '44': '0000 000 000',
                '81': '000-0000-0000',
                '86': '000 0000 0000',
                '91': '00000 00000',
            };
            this.placeholder = masks[this.selectedCountry] || '000 000 0000';
        },

        formatPhone() {
            let digits = this.localValue.replace(/\D/g, '');
            const maxDigits = { '63': 10, '1': 10, '44': 10, '81': 11, '86': 11, '91': 10 };
            const max = maxDigits[this.selectedCountry] || 15;
            digits = digits.slice(0, max);

            if (this.selectedCountry === '63') {
                if (digits.length > 3) digits = digits.slice(0, 3) + ' ' + digits.slice(3);
                if (digits.length > 7) digits = digits.slice(0, 7) + ' ' + digits.slice(7);
            } else if (this.selectedCountry === '1') {
                if (digits.length > 3) digits = '(' + digits.slice(0, 3) + ') ' + digits.slice(3);
                if (digits.length > 9) digits = digits.slice(0, 9) + '-' + digits.slice(9);
            } else if (this.selectedCountry === '81' || this.selectedCountry === '82') {
                if (digits.length > 3) digits = digits.slice(0, 3) + '-' + digits.slice(3);
                if (digits.length > 8) digits = digits.slice(0, 8) + '-' + digits.slice(8);
            } else {
                if (digits.length > 4) digits = digits.slice(0, 4) + ' ' + digits.slice(4);
                if (digits.length > 9) digits = digits.slice(0, 9) + ' ' + digits.slice(9);
            }

            this.localValue = digits;
        },

        get fullNumber() {
            return '+' + this.selectedCountry + ' ' + this.localValue.replace(/\D/g, '');
        }
    }));
});
</script>
