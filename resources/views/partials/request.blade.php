### {{ $route->label() }} [{{ $route->method() }}]

+ Request Test

@unless($route->isGet())
    + Body

            {
@foreach($route->parameter() as $parameter)
                "{{ urlencode($parameter->getName()) }}": "{!! $parameter->getExample() !!}", // [{{ $parameter->getType() }}] {!! $parameter->getComment() !!}
@endforeach
            }
@endunless

    + Headers

@if ($route->isSecure())
            Authorization: Bearer YourBearerToken...
@endif
            Content-Type: application/vnd.api+json
            Accept: application/vnd.api+json
