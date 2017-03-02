    + Schema

            {
                "type": "object",
                "properties": {
@foreach($resource->getAttributes() as $attribute)
                  "{{ $attribute->getName() }}": {
                      "type": "{{ $attribute->getType() }}",
                      "description": "{!! $attribute->getComment() !!}"
                  },
@endforeach
                },
                "required": [
                    {!! $resource->getRequiredAttributesString() !!}
                ],
                "$schema": "http://json-schema.org/draft-04/schema#"
            }
