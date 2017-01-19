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

    public function toXML()
    {
        return $this->read(false, true);
    }

    /**
     * Read the response body from the API
     * @param  boolean $toArray Whether to return as an array
     * @param  boolean $toXML   Whether to return as xml
     * @return mixed            The processed read response
     */
    private function read($toArray = false, $toXML = false)
    {
        $xml = new \SimpleXMLElement($this->response->getbody()->getContents());

        if ($toXML) {
            return $xml;
        }

        \Log::info(json_encode($xml));

        return json_decode(json_encode($xml->{$this->method}), $toArray);
    }
}
