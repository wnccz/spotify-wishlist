<?php

namespace Wnc\Wishlist;

use DateInterval;
use GuzzleHttp\Client;
use Nette\Caching\Cache;
use Nette\Http\Url;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class spotifyService {
    private $clientId;
    private $clientSecret;

    /** @var \Nette\Caching\Cache */
    public $cache;

    /**
     * Public constructor
     * @param \Nette\Caching\Cache $cache Cachce object
     */
    public function __construct(\Nette\Caching\Cache $cache, $clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->cache = $cache;
    }

    /**
     * Query to spotify API service
     * @var string $endpoint last parts or URL of web service
     * @var array $data array of data in GET or POST attributes
     * @var string $token access token
     * @var string method
     */
    private function query($endpoint, $data, $token = null, $method="GET") {
        if ($token === null) {
            $token = $this->getClientToken();
        }
        $client = new Client([
            'base_uri' => "https://api.spotify.com/v1/"
        ]);
	$headers = ['Authorization' => "Bearer $token"];
	try {
          return $client->request($method, $endpoint, [
            'query' => $data,
            'headers' => $headers
          ]);
	} catch (\GuzzleHttp\Exception\RequestException $e) {
		$data = json_decode($e->getResponse()->getBody());
		if ($data->error->reason == "NO_ACTIVE_DEVICE") {
			throw new \Wnc\NoActiveDeviceException($data->error->message);
		}
		throw new \Wnc\SpotifyServiceException($data->error->message);
	}
    }

    public function clientAuth() {
        $client = new Client([
            'base_uri' => "https://accounts.spotify.com/api/"
        ]);
        $response = $client->request('POST', 'token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);
        $data = json_decode($response->getBody());
        return  $data->access_token;
    }

    public function userAuth($code, $redirectUri) {
        $client = new Client([
            'base_uri' => "https://accounts.spotify.com/api/token"
        ]);
        $response = $client->request('POST', 'token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri
            ]
        ]);
        $data = json_decode($response->getBody());
        return  $data;
    }

    public function getClientToken() {
        return $this->cache->load('auth_token', function (&$dependencies) {
            $dependencies[Cache::EXPIRE] = '20 minutes';
            return $this->clientAuth();
        });
    }

    public function searchSongs($query) {
        $data = $this->query("search", [
            'q' => $query,
            'type' => "track",
            'limit' => 12
        ]);
        return json_decode($data->getBody());
    }

    public function getAuthUrl($redirectUri) {
        $query = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => 'user-read-email, app-remote-control, user-modify-playback-state',
            'redirect_uri' => $redirectUri,
            'state' => "asdEasd4a5e869a5df45"
        ];
        $url = new Url("https://accounts.spotify.com/authorize");
        $url->setQuery($query);
        return (string)$url;
    }

    public function getUserProfile($token) {
        $data = $this->query("me", [], $token);
        return json_decode($data->getBody());
    }

    public function getTrackById($trackId) {
        $data = $this->query("tracks/$trackId", []);
        return json_decode($data->getBody());
    }

    public function addToQueue($trackId, $token) {
        $this->query("me/player/queue", ["uri" => "spotify:track:$trackId"], $token, "POST");
    }
    public function refreshAccessToken($refreshToken) {
        $client = new Client([
            'base_uri' => "https://accounts.spotify.com/api/token"
        ]);
        $response = $client->request('POST', 'token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken
            ]
        ]);
        $data = json_decode($response->getBody());
        return  $data;
    }

    public function getAccessToken($identity) {
        if ($identity->token_validity < new DateTime()) {
            $data = $this->refreshAccessToken($identity->refresh_token);
            $identity->token = $data->access_token;
            $date = new DateTime();
            $date->add(new DateInterval("PT".$data->expires_in."S"));
            $identity->token_validity = $date;
        }
        return $identity->token;
    }
}
