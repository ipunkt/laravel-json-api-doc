# Group {{ $resource->getName() }}
{!! $resource->getComment() !!}

@unless($resource->itemRoutes()->isEmpty())
Existing calls:
@foreach ($resource->itemRoutes() as $route)
- Item call: <xmp>{{ urldecode($route->path()) }}</xmp>
@endforeach
@foreach($resource->getRelations() as $relation)
- Relation for <kbd>{{ $relation->getName() }}: {{ $relation->getComment() }}</kbd> <xmp>{{ $resource->itemRoutes()->first()->path() }}/relationships/{{ $relation->getName() }}</xmp>
@endforeach
@foreach ($resource->collectionRoutes() as $route)
- Listing call: <xmp>{{ urldecode($route->path()) }}</xmp>
@endforeach

@foreach ($resource->itemRoutes() as $route)
@include('laravel-json-api-doc::route', compact('route'))
@endforeach

@endunless

@foreach ($resource->collectionRoutes() as $route)
@include('laravel-json-api-doc::route', compact('route'))
@endforeach
