<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-tv-pink border border-transparent rounded-xl font-urbanist font-bold text-sm text-white hover:bg-tv-pink/90 focus:bg-tv-pink/90 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-tv-pink focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-tv-pink/30']) }}>
    {{ $slot }}
</button>
