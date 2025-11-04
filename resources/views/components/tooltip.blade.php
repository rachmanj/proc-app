@props(['title', 'placement' => 'top', 'icon' => 'info-circle'])

<span data-toggle="tooltip" data-placement="{{ $placement }}" title="{{ $title }}" class="text-info ml-1" style="cursor: help;">
    <i class="fas fa-{{ $icon }}"></i>
</span>

