<?php

namespace App\Infrastructure\Framework\ApiResponse;

class CreateOwnerFailedResponse extends AbstractApiResponse
{
    private string $errMsg;

    /**
     * @param string $errMsg
     */
    public function __construct(string $errMsg)
    {
        $this->errMsg = $errMsg;
    }

    public function getStatusCode(): int
    {
        return 400;
    }

    public function getHeaders(): array
    {
        return [
            'Error' => $this->errMsg
        ];
    }
}
