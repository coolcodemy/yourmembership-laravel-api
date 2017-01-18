<?php

namespace CoolCodeMY\YourMembershipLaravelAPI\Libraries;

class Response
{
    private $method;
    private $response;

    public function __construct($method, \GuzzleHttp\Psr7\Response $response)
    {
        $this->method   = $method;
        $this->response = $response;
    }

    public function toArray()
    {
        return $this->read(true);
    }

    public function toJson()
    {
        return $this->read();
    }

    private function read($toArray = false)
    {
        $xml = new \SimpleXMLElement($this->response->getbody()->getContents());

        \Log::info(json_encode($xml));

        return json_decode(json_encode($xml->{$this->method}), $toArray);
    }
}
