<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-bg-primary px-4 py-2    text-white   transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
