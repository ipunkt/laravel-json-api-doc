## {{ $resource->getName() }} [{{ $route->path() . $route->getParameterQueryString($resource->getRelations(), ['id']) }}]

@include('laravel-json-api-doc::partials.parameter')

@include('laravel-json-api-doc::partials.request')

@foreach($route->getResponses() as $responseCode => $response)
@if( empty($response) )
@include('laravel-json-api-doc::partials.response-'.$responseCode)
@else
+ Response {!! $responseCode !!}

{!! preg_replace('~^~m', "\t\t\t", $response) !!}
@endif
@endforeach
