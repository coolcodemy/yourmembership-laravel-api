<?php

namespace CoolCodeMY\YourMembershipLaravelAPI\Exceptions;

use CoolCodeMY\YourMembershipLaravelAPI\Exceptions\YourMembershipException;

class ResponseException extends YourMembershipException
{
    private $apiMethod;

    public function __construct(string $message, int $code = 0, string $apiMethod, \Exception $e = null)
    {
        $this->apiMethod = $apiMethod;
        parent::__construct($message, $code, $e);
    }

    public function getApiMethodName(): string
    {
        return $this->apiMethod;
    }
    public function __toString()
    {
        return __CLASS__ . ": [{$this->apiMethod}]: {$this->message}\n";
    }
}
