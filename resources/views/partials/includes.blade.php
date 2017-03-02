@if(count($resource->getRelations()))
+ include: `{{ $resource->possibleRelationsString() }}` (string, optional) - comma-separated list of possible includes
@endif