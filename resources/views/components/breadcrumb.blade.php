{{-- Reusable Breadcrumb Component --}}
@props(['items' => []])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i> Home
            </a>
        </li>

        @foreach($items as $item)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    @if(isset($item['icon']))
                        <i class="{{ $item['icon'] }}"></i>
                    @endif
                    {{ $item['title'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}">
                        @if(isset($item['icon']))
                            <i class="{{ $item['icon'] }}"></i>
                        @endif
                        {{ $item['title'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
