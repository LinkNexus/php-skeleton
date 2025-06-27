<?php

namespace App\Controller;

use App\Core\Response;
use App\Router\Attributes\Route;

class AppController
{
    #[Route("/")]
    public function index(): Response
    {
        return new Response("Hello World!");
    }

}
