@php
    $hasChildren = !empty($item['children']) && count($item['children']) > 0;
    $url = $item['url'] ?? '#';
    $title = $item['title'] ?? '';
    // Ensure absolute URL
    $fullUrl = Str::startsWith($url, ['http://', 'https://']) ? $url : url($url);
    $isRoot = $level === 0;
@endphp

@if(!$hasChildren)
    <a href="{{ $fullUrl }}" class="{{ $isRoot ? 'text-[15px] font-semibold text-[#181611] dark:text-gray-200 hover:text-primary dark:hover:text-primary transition-all whitespace-nowrap' : 'block mx-1.5 px-3 py-2.5 rounded-xl text-sm text-gray-700 dark:text-gray-300 hover:bg-primary/5 dark:hover:bg-white/5 hover:text-primary dark:hover:text-primary font-medium transition-colors whitespace-nowrap relative after:absolute after:bottom-0 after:left-3 after:right-3 after:h-[1px] after:bg-gray-100 dark:after:bg-white/5 last:after:hidden' }}">
        {{ $title }}
    </a>
@else
    <div class="relative {{ $isRoot ? 'flex items-center h-full' : 'w-full px-1.5' }} 
        [&:hover>.dropdown-menu]:opacity-100 [&:hover>.dropdown-menu]:visible [&:hover>.dropdown-menu]:delay-0
        {{ $isRoot ? '[&:hover>.dropdown-menu]:translate-y-0 [&:hover>a]:text-primary dark:[&:hover>a]:text-primary [&:hover>a>span]:-rotate-180' : '[&:hover>.dropdown-menu]:translate-x-0 [&:hover>a]:bg-primary/5 dark:[&:hover>a]:bg-white/5 [&:hover>a]:text-primary dark:[&:hover>a]:text-primary [&:hover>a>span]:-rotate-90' }}
    ">
        
        <a href="{{ $fullUrl }}" class="{{ $isRoot ? 'text-[15px] font-semibold text-[#181611] dark:text-gray-200 hover:text-primary dark:hover:text-primary transition-all flex items-center gap-1 whitespace-nowrap py-2' : 'flex items-center justify-between px-3 py-2.5 rounded-xl text-sm text-gray-700 dark:text-gray-300 hover:bg-primary/5 dark:hover:bg-white/5 hover:text-primary dark:hover:text-primary font-medium transition-colors w-full whitespace-nowrap relative after:absolute after:bottom-0 after:left-3 after:right-3 after:h-[1px] after:bg-gray-100 dark:after:bg-white/5' }} 
            {{ !$isRoot && $loop->last ? 'after:hidden' : '' }}">
            {{ $title }}
            <span class="material-symbols-outlined text-[18px] transition-transform duration-300 {{ $isRoot ? '' : '-rotate-90' }}">expand_more</span>
        </a>
        
        <!-- Dropdown container -->
        <div class="dropdown-menu absolute z-50 opacity-0 invisible transition-all duration-300 delay-150
            {{ $isRoot ? 'top-[calc(100%-0.5rem)] left-0 pt-4 translate-y-3' : 'top-4 left-[calc(100%-0.5rem)] pl-2 translate-x-2' }}
        ">
            
            <!-- Invisible bridge to keep hover state active while mouse is moving -->
            @if(!$isRoot)
                <div class="absolute top-0 -left-4 w-6 h-full bg-transparent"></div>
            @else
                <div class="absolute -top-4 left-0 w-full h-8 bg-transparent"></div>
            @endif

            <div class="bg-white/95 dark:bg-[#221e10]/95 backdrop-blur-xl border border-gray-100 dark:border-white/10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.3)] min-w-[240px] flex flex-col p-1.5">
                @foreach($item['children'] as $child)
                    @include('client.layouts.menu-item', ['item' => $child, 'level' => $level + 1])
                @endforeach
            </div>
        </div>
    </div>
@endif
