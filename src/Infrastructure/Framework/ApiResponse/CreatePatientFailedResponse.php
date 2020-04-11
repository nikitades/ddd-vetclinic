<?php

namespace App\Infrastructure\Framework\ApiResponse;

class CreatePatientFailedResponse extends AbstractApiResponse
{
    public function getStatusCode(): int
    {
        return 400;
    }

    public function getHeaders(): array
    {
        return [
            "Error" => "Failed to create a patient"
        ];
    }
}
