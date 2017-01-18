<?php

namespace CoolCodeMY\YourMembershipLaravelAPI;

use CoolCodeMY\YourMembershipLaravelAPI\Libraries\Request;
use CoolCodeMY\YourMembershipLaravelAPI\Libraries\Response;
use GuzzleHttp\Client;
use Illuminate\Cache\Repository as Cache;

class YMLA
{
    private $client;
    private $cache;
    private $apiKey;
    private $saPasscode;
    private $request;

    public function __construct(Client $client, Cache $cache, $apiKey, $saPasscode)
    {
        $this->client     = $client;
        $this->cache      = $cache;
        $this->apiKey     = $apiKey;
        $this->saPasscode = $saPasscode;
        $this->request    = new Request($apiKey, $saPasscode);
        Request::setSessionID($this->getSessionID());
    }

    public function call($method, $args = [])
    {
        $request  = $this->request->create($method, $args);
        $response = $this->client->send($request);

        return new Response($method, $response);
    }

    public function getSessionID()
    {
        $that = $this;

        return $this->cache->remember('YMLA-SessionID', 15, function () use ($that) {
            $data = $that->call('Session.Create')->toJson();
            return $data->SessionID;
        });
    }
}
