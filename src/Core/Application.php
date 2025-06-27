<?php

namespace App\Core;

use App\Router\Router;
use App\Core\Request;

class Application
{
    private readonly Router $_router;
    private readonly Request $_request;


    public function __construct(
        private readonly string $_projectDir
    ) {
        $this->_request = new Request();
        $this->_router = new Router($this->_request, $this->_projectDir);
    }

    public function run(): void
    {
        $this->_router->handleRequest();
    }

}
