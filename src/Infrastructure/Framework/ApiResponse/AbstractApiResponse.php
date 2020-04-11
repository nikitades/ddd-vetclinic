<?php

namespace App\Infrastructure\Framework\ApiResponse;

use App\Domain\Shared\Exception\WrongValueException;
use JsonSerializable;
use phpDocumentor\Reflection\Types\Object_;

abstract class AbstractApiResponse implements JsonSerializable
{
    public function getStatusCode(): int
    {
        throw new WrongValueException("You must define some HTTP status code");
    }

    /**
     * Gets a header bag for this response;
     *
     * @return array<mixed>
     */
    public function getHeaders(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return (object)[];
    }
}
