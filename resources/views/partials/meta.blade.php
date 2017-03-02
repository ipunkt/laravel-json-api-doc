@if(count($metas)),
            "meta": {
@foreach($metas as $meta)
                "{{ $meta->getName() }}": "{{ $meta->getType() }}, {{ $meta->getComment() }}"@unless($meta == end($metas)),@endunless
@endforeach
            }
@endif