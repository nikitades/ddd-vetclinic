<?php

namespace App\Infrastructure\Framework\ApiResponse;

use App\Domain\Patient\Entity\Owner;

class CreateOwnerSuccessResponse extends AbstractApiResponse
{
    use JsonConverting;

    private Owner $owner;

    public function __construct(Owner $owner)
    {
        $this->owner = $owner;
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    /** @return mixed */
    public function jsonSerialize()
    {
        return $this->json($this->owner);
    }
}