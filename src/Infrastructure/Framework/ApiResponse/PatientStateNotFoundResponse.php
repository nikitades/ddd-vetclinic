<?php

namespace App\Infrastructure\Framework\ApiResponse;

class PatientStateNotFoundResponse extends AbstractApiResponse
{
    private string $msg;

    public function __construct(string $msg = "The patient was not found")
    {
        $this->msg = $msg;
    }

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
            "Error" => $this->msg
        ];
    }

    public function jsonSerialize()
    {
        return [];
    }
}
