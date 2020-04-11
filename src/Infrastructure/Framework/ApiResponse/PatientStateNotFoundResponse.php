<?php

namespace App\Infrastructure\Framework\ApiResponse;

class PatientStateNotFoundResponse extends AbstractApiResponse
{
    public function getStatusCode(): int
    {
        return 400;
    }

    /**
     * Provides the error header
     *
     * @return array<string>
     */
    public function getHeaders(): array
    {
        return [
            "Error" => "The patient was not found"
        ];
    }

    public function jsonSerialize()
    {
        return [];
    }
}
