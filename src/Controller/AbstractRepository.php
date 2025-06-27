<?php

namespace App\Controller;

abstract class AbstractRepository
{
    /**
     * The project directory.
     *
     * @var string
     */
    protected string $_projectDir;

    /**
     * The request object.
     *
     * @var \App\Core\Request
     */
    protected \App\Core\Request $_request;

    /**
     * The routes registered in the application.
     *
     * @var array<string, array>
     */
    protected array $_routes = [];

    /**
     * Constructor.
     *
     * @param string            $projectDir
     * @param \App\Core\Request $request
     */
    public function __construct(string $projectDir, \App\Core\Request $request)
    {
        $this->_projectDir = $projectDir;
        $this->_request = $request;
    }

    // Other methods would go here...
}
