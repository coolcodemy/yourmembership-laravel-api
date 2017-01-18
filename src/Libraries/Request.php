<?php

namespace CoolCodeMY\YourMembershipLaravelAPI\Libraries;

class Request
{
    const VERSION   = '2.25';
    const END_POINT = 'https://api.yourmembership.com';

    private $apiKey;
    private $saPasscode;

    private static $sessionID;

    public function __construct($apiKey, $saPasscode)
    {
        $this->apiKey     = $apiKey;
        $this->saPasscode = $saPasscode;
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
        $parent = $this->getCommon();
        $parent->addChild('SessionID', self::$sessionID);

        $call = new \SimpleXMLElement("<Call></Call>");
        $call->addAttribute('Method', $method);
        $call = $this->addRecursiveChildren($call, $args);

        $xml = $this->merge($parent, $call);

        return $xml;
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

    private function getCommon()
    {
        $xml = new \SimpleXMLElement("<YourMembership></YourMembership>");
        $xml->addChild('Version', self::VERSION);
        $xml->addChild('ApiKey', $this->apiKey);
        $xml->addChild('CallID', uniqid('', true));

        return $xml;
    }

    public static function setSessionID($id)
    {
        self::$sessionID = $id;
    }
}
