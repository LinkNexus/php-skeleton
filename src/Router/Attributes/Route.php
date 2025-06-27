<?php

namespace App\Router\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Route
{
    /**
     * @param string                $path         The path for the route
     * @param array                 $methods      The HTTP methods allowed for this route, default is ["GET"]
     * @param array<string, string> $requirements Additional requirements for the route parameters, default is an empty array
     * @param string|null           $name         The name of the route, default is null
     */
    public function __construct(
        public string $path,
        public array $methods = ["GET"],
        public array $requirements = [],
        public ?string $name = null
    ) {
    }

}
