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
    private $secretApiKey;
    private $saPasscode;
    private $request;
    private $illuminateRequest;

    public function __construct(Client $client, Cache $cache, \Illuminate\Http\Request $illuminateRequest, $apiKey, $secretApiKey, $saPasscode)
    {
        $this->client            = $client;
        $this->cache             = $cache;
        $this->illuminateRequest = $illuminateRequest;
        $this->apiKey            = $apiKey;
        $this->secretApiKey      = $secretApiKey;
        $this->saPasscode        = $saPasscode;
        $this->request           = new Request($apiKey, $secretApiKey, $saPasscode);
    }

    public function call($method, $args = [], $bypassCheckSession = true)
    {
        if ($bypassCheckSession) {
            Request::setSessionID($this->getSessionID());
        }

        $request  = $this->request->create($method, $args);
        $response = $this->client->send($request);

        return new Response($method, $response);
    }

    public function getSessionID()
    {
        $that     = $this;
        $cacheKey = sprintf('YMLA-SessionID-%s', $this->illuminateRequest->fingerprint());

        return $this->cache->remember($cacheKey, 15, function () use ($that) {
            $data = $that->call('Session.Create', [], false)->toJson();
            return $data->SessionID;
        });
    }
}
