@props(['size' => 'md', 'theme' => 'dark', 'badge' => null])

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 112" fill="none" class="{{ $size === 'sm' ? 'w-7 h-8' : ($size === 'lg' ? 'w-11 h-12' : 'w-9 h-10') }} shrink-0">
    <defs>
      <linearGradient id="taGradLeft-{{ $attributes->get('id', 'main') }}" x1="12" y1="88" x2="48" y2="12" gradientUnits="userSpaceOnUse">
        <stop offset="0%" stop-color="#11653A" />
        <stop offset="100%" stop-color="#2FA33E" />
      </linearGradient>
      <linearGradient id="taGradRight-{{ $attributes->get('id', 'main') }}" x1="50" y1="104" x2="88" y2="12" gradientUnits="userSpaceOnUse">
        <stop offset="0%" stop-color="#2D8B37" />
        <stop offset="100%" stop-color="#80C826" />
      </linearGradient>
      <mask id="taPinHole-{{ $attributes->get('id', 'main') }}">
        <rect width="100" height="112" fill="white" />
        <circle cx="50" cy="49" r="19.5" fill="black" />
      </mask>
    </defs>
    <g mask="url(#taPinHole-{{ $attributes->get('id', 'main') }})">
      <path d="M50 6 L13 19.5 C10.5 48 16 78 50 106 Z" fill="url(#taGradLeft-{{ $attributes->get('id', 'main') }})" />
      <path d="M50 6 L87 19.5 C89.5 48 84 78 50 106 Z" fill="url(#taGradRight-{{ $attributes->get('id', 'main') }})" />
    </g>
  </svg>
  <span class="{{ $size === 'sm' ? 'text-lg' : ($size === 'lg' ? 'text-2xl' : 'text-xl') }} tracking-tight leading-none {{$theme === 'light' ? 'text-slate-900' : 'text-white' }}">
    <strong class="font-extrabold">Titik</strong><span class="font-normal">Aman</span>
  </span>
  @if($badge)
    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">{{ $badge }}</span>
  @endif
</div>
