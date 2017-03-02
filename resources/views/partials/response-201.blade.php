@if ($route->isPost())
+ Response 201

    + Headers

            Server: nginx/1.9.7
            Content-Type: application/vnd.api+json
            Transfer-Encoding: chunked
            Connection: keep-alive
            Cache-Control: max-age=600, must-revalidate, private
            Date: Mon, 18 Jul 2016 14:11:29 GMT

    + Body

            {
                "data": [
                    @include("laravel-json-api-doc::partials.resource"),
                ]
@include('laravel-json-api-doc::partials.meta', array('metas' => $resource->getMeta()))
            }

    @include('laravel-json-api-doc::partials.schema')

@endif
