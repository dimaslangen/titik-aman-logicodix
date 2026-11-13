@props(['size' => 'md', 'theme' => 'dark', 'badge' => null])

@php
    $dimensions = [
        'sm' => ['width' => '26px', 'height' => '30px', 'fontSize' => '1.125rem'],
        'lg' => ['width' => '42px', 'height' => '46px', 'fontSize' => '1.5rem'],
        'md' => ['width' => '34px', 'height' => '38px', 'fontSize' => '1.25rem'],
    ][$size] ?? ['width' => '34px', 'height' => '38px', 'fontSize' => '1.25rem'];
    $textColor = $theme === 'light' ? '#0f172a' : '#ffffff';
@endphp

<div {{ $attributes->merge(['class' => 'd-inline-flex align-items-center gap-2 inline-flex items-center gap-2.5 select-none']) }} style="line-height: 1; user-select: none;">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 112" fill="none" class="{{ $size === 'sm' ? 'w-7 h-8' : ($size === 'lg' ? 'w-11 h-12' : 'w-9 h-10') }} shrink-0" style="width: {{ $dimensions['width'] }}; height: {{ $dimensions['height'] }}; flex: 0 0 auto;">
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
  <span class="{{ $size === 'sm' ? 'text-lg' : ($size === 'lg' ? 'text-2xl' : 'text-xl') }} tracking-tight leading-none {{$theme === 'light' ? 'text-slate-900' : 'text-white' }}" style="color: {{ $textColor }}; font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; font-size: {{ $dimensions['fontSize'] }}; font-weight: 400; white-space: nowrap;">
    <strong class="font-extrabold" style="font-weight: 800;">Titik</strong><span class="font-normal" style="font-weight: 400;">Aman</span>
  </span>
  @if($badge)
    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-emerald-500/15 text-emerald-400 border border-emerald-500/30" style="font-size: 10px; font-weight: 700; letter-spacing: .08em; white-space: nowrap;">{{ $badge }}</span>
  @endif
</div>
