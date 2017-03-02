FORMAT: {{ $format or '1A' }}
HOST: {{ config('app.url') }}

# {{ $name }}
{{ $description }}

@foreach($resources as $resource)

{!! $resource !!}

@endforeach
