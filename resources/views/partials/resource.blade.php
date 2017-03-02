                    {
                        "type":"{{ $resource->getType() }}",
                        "id":"{id}",
@if(count($resource->getAttributes()))
                        "attributes": {
@foreach($resource->getAttributes() as $attribute)
                            "{{ $attribute->getName() }}": {!! $attribute->example() !!},
@endforeach
                        },
@endif
@if( 0 < count($resource->getLinks()))
                        "links": {
@foreach($resource->getLinks() as $link)
                            "{{ $link->getName() }}":"{{ $link->getComment() }}",
@endforeach
                        },
@endif
@if( 0 < count($resource->getRelations()))
                        "relationships": {
@foreach($resource->getRelations() as $relation)
                            "{{ $relation->getName() }}": {[
                                "type":"{{ $relation->getType() }}",
                                "id":"{relations id}"
                            ]}
@endforeach
                        }
@endif
                    }