<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class EmptyController
{
    public function __invoke(mixed $data): Response
    {
        return new Response(
            json_encode($data),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}