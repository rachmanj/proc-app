@props(['date', 'label' => 'Last Updated'])

@if($date)
    <small class="text-muted">
        <i class="far fa-clock"></i> {{ $label }}: {{ $date->diffForHumans() }}
        <span class="ml-1">({{ $date->format('Y-m-d H:i') }})</span>
    </small>
@endif

