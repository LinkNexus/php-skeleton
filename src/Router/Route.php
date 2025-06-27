<?php

namespace App\Router;

use App\Core\Response;

class Route
{
    private readonly string $_path;
    private array $_matches = [];
    private readonly array $_requirements;

    /**
     * Constructs a new Route instance.
     *
     * @param \Closure              $callback     The callback to be executed when the route is matched
     * @param string                $path         The path for the route
     * @param array<string, string> $requirements Optional requirements for route parameters
     */
    public function __construct(
        private readonly \Closure $callback,
        string $path,
        array $requirements = []
    ) {
        $this->_path = trim($path, "/");
        $this->_requirements = array_map(
            fn ($req) => str_replace("(", "(?:", $req),
            $requirements
        );
    }

    public function match(string $url): bool
    {
        $url = trim($url, '/');
        preg_match_all("#:([\w]+)#", $this->_path, $paramNames);

        $pattern = preg_replace_callback("#:([\w]+)#", [$this, "_matchParams"], $this->_path);
        $regex = "#^$pattern$#";

        if (preg_match($regex, $url, $matches)) {
            array_shift($matches);
            $this->_matches = array_combine($paramNames[1], $matches);
            return true;
        }

        return false;
    }

    private function _matchParams(array $match): string
    {
        if (isset($this->_requirements[$match[1]])) {
            return "({$this->_requirements[$match[1]]})";
        }

        return "([^/]+)";
    }

    public function call(): void
    {
        $reflection = new \ReflectionFunction($this->callback);
        $params = [];

        foreach ($reflection->getParameters() as $param) {
            $value = $this->_matches[$param->getName()];

            if (array_key_exists($param->getName(), $this->_matches)
                || $param->isOptional()
            ) {

                if ($param->getType() && $param->getType()->getName() === "int") {
                    $value = (int)$value;
                }

                $params[$param->getName()] = $value;
            } else {
                throw new \InvalidArgumentException(
                    "Missing required parameter: " . $param->getName()
                );
            }
        }

        $response = call_user_func_array($this->callback, $params);
        $this->_processResponse($response);
    }

    private function _processResponse(mixed $response): void
    {

        if ($response instanceof Response) {
            http_response_code($response->getStatusCode());

            foreach ($response->getHeaders() as $header) {
                header($header);
            }

            echo $response->getContent();
        } elseif (is_string($response) || is_numeric($response)) {
            echo $response;
        } else {
            header("Content-Type: application/json");
            echo json_encode($response);
        }

    }
}
