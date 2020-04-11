<?php

namespace App\Infrastructure\Framework\ApiResponse;

class AddPatientToOwnerFailedResponse extends AbstractApiResponse
{
    private string $msg;

    public function __construct(string $msg)
    {
        $this->msg = $msg;
    }

    public function getStatusCode(): int
    {
        return 400;
    }

    public function getHeaders(): array
    {
        return [
            'Error' => $this->msg
        ];
    }
}
