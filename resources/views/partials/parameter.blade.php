@if (!$route->isPost())
@unless ($route->parameter()->isEmpty())
+ Parameters
@foreach($route->parameter() as $parameter)
    + {{ urlencode($parameter->getName()) }}: `{!! $parameter->getExample() !!}` ({{ $parameter->getType() }}) - {{ $parameter->getComment() }}
@endforeach
    @include('laravel-json-api-doc::partials.includes')
@endunless
@endif
