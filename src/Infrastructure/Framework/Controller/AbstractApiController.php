<?php

namespace App\Infrastructure\Framework\Controller;

use App\Infrastructure\Framework\ApiResponse\AbstractApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    protected function apiResponse(AbstractApiResponse $response): Response
    {
        return $this->json($response, $response->getStatusCode(), $response->getHeaders());
    }
}
