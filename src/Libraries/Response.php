<?php

namespace CoolCodeMY\YourMembershipLaravelAPI\Libraries;

use CoolCodeMY\YourMembershipLaravelAPI\Exceptions\ResponseException;

class Response
{
    private $method;
    private $response;

    public function __construct($method, \GuzzleHttp\Psr7\Response $response)
    {
        $this->method   = $method;
        $this->response = new \SimpleXMLElement($response->getbody()->getContents());
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

    public function hasError()
    {
        return $this->getErrCode() != 0;
    }

    public function getErrCode()
    {
        return (int) $this->response->ErrCode;
    }

    public function getError()
    {
        return (string) $this->response->ErrDesc;
    }

    /**
     * Read the response body from the API
     * @param  boolean $toArray Whether to return as an array
     * @param  boolean $toXML   Whether to return as xml
     * @return mixed            The processed read response
     */
    private function read($toArray = false, $toXML = false)
    {
        if ($this->hasError()) {
            throw new ResponseException($this->getError(), $this->getErrCode(), $this->method);
        }

        if ($toXML) {
            return $this->response;
        }
        return json_decode(json_encode($this->response->{$this->method}), $toArray);
    }
}
