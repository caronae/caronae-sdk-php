<?php

namespace Caronae;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class CaronaeService
{
    const PRODUCTION_API_URL = "https://api.caronae.org";

    private $client;
    private $institutionID;
    private $institutionPassword;
    private $token;
    private $apiURL;

    public function __construct($apiURL = self::PRODUCTION_API_URL, Client $client = null)
    {
        $this->apiURL = $apiURL;

        if ($client == null) {
            $client = new Client([
                'base_uri' => $this->apiURL,
                'timeout' => 15.0,
                'headers' => ['Accept' => 'application/json']
            ]);
        }

        $this->client = $client;
    }

    public function setInstitution($institutionID, $institutionPassword)
    {
        $this->institutionID = $institutionID;
        $this->institutionPassword = $institutionPassword;
    }

    public function authorize($user)
    {
        $this->verifyInstitutionWasSet();

        try {
            $response = $this->client->post('/api/v1/users', ['json' => $user, 'auth' => $this->authorization()]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if (!$this->isResponseValid($response)) {
                throw new CaronaeException("Invalid response from Caronae API (status code: " . $response->getStatusCode() . ")");
            }

            throw new CaronaeException($e->getMessage());
        }

        $responseBody = json_decode($response->getBody());
        if (empty($responseBody->token)) {
            throw new CaronaeException("Invalid response from Caronae API (token was empty for user " . $user['id_ufrj'] . ")");
        }

        $this->token = $responseBody->token;
        return $responseBody;
    }

    public function redirectUrlForSuccess()
    {
        return $this->apiURL . '/login?token=' . $this->token;
    }

    public function redirectUrlForError($reason)
    {
        return $this->apiURL . '/login?error=' . $reason;
    }

    private function authorization()
    {
        return [ $this->institutionID, $this->institutionPassword ];
    }

    private function verifyInstitutionWasSet()
    {
        if (empty($this->institutionID) || empty($this->institutionPassword)) {
            throw new CaronaeException("You need to set the Caronae institution before making calls.");
        }
    }

    private function isResponseValid(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        return $statusCode >= 200 && $statusCode < 300;
    }
}

class CaronaeException extends \Exception {}
