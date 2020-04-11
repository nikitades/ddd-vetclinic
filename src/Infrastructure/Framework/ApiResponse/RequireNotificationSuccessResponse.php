<?php

namespace App\Infrastructure\Framework\ApiResponse;

use App\Domain\Patient\Entity\Owner;

class RequireNotificationSuccessResponse extends AbstractApiResponse
{
    private Owner $owner;

    public function __construct(Owner $owner)
    {
        $this->owner = $owner;
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'email' => $this->owner->getEmail()
        ];
    }
}
