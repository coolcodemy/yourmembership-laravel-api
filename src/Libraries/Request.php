<?php

namespace CoolCodeMY\YourMembershipLaravelAPI\Libraries;

class Request
{
    const VERSION   = '2.25';
    const END_POINT = 'https://api.yourmembership.com';

    private $apiKey;
    private $secretApiKey;
    private $saPasscode;

    private static $sessionID;

    public function __construct($apiKey, $secretApiKey, $saPasscode)
    {
        $this->apiKey       = $apiKey;
        $this->secretApiKey = $secretApiKey;
        $this->saPasscode   = $saPasscode;
    }

    public function create($method, $args = [])
    {
        $xml = $this->createCall($method, $args);

        return new \GuzzleHttp\Psr7\Request(
            'POST',
            self::END_POINT,
            ['Content-Type' => 'application/x-www-form-urlencoded; charset=UTF8'],
            $xml->asXML()
        );
    }

    public function createCall($method, $args = [])
    {
        $parent = $this->getCommon($method);
        $this->addSessionToMethod($parent, $method);

        $call = new \SimpleXMLElement("<Call></Call>");
        $call->addAttribute('Method', $method);
        $call = $this->addRecursiveChildren($call, $args);

        $xml = $this->merge($parent, $call);

        return $xml;
    }

    private function addSessionToMethod($ymParent, $method)
    {
        if (strpos($method, 'Sa.') !== 0) {
            $ymParent->addChild('SessionID', self::$sessionID);
        } else if (strpos($method, 'Sa.Auth.') === 0) {
            $ymParent->addChild('SessionID', self::$sessionID);
        }
    }

    private function addRecursiveChildren($call, $args = [])
    {
        foreach ($args as $key => $value) {
            if (is_array($value)) {
                $child = new SimpleXMLElement(sprintf("<%s></%s>", $key, $key));
                $child = $this->addRecursiveChildren($child, $value);
                $child = $this->merge($call, $child);
            } else {
                $call->addChild($key, $value);
            }
        }

        return $call;
    }

    private function merge(\SimpleXMLElement $parent, \SimpleXMLElement $child)
    {
        $domParent = dom_import_simplexml($parent);
        $domChild  = dom_import_simplexml($child);

        $domParent->appendChild($domParent->ownerDocument->importNode($domChild, true));

        $xml = simplexml_import_dom($domParent);

        return $xml;
    }

    private function getCommon($method)
    {
        $xml = new \SimpleXMLElement("<YourMembership></YourMembership>");
        $xml->addChild('Version', self::VERSION);
        $this->addSaIfAdmin($xml, $method);
        $xml->addChild('CallID', uniqid('', true));

        return $xml;
    }

    private function addSaIfAdmin($xml, $method)
    {
        if (strpos($method, 'Sa.') === 0) {
            $xml->addChild('SaPasscode', $this->saPasscode);
            $xml->addChild('ApiKey', $this->secretApiKey);
        } else {
            $xml->addChild('ApiKey', $this->apiKey);
        }
    }

    public static function setSessionID($id)
    {
        self::$sessionID = $id;
    }
}
