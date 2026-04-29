@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border-gray-200 focus:border-tv-blue focus:ring-tv-blue/20 rounded-xl shadow-sm px-4 py-3 text-gray-800 placeholder-gray-400 transition-colors']) }}>
