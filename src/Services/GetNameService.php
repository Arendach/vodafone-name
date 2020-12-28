<?php

namespace Vodafone\Name\Services;

use Faker\Factory;
use GuzzleHttp\Client;
use Exception;

class GetNameService
{
    private $isTesting = false;

    public function __construct()
    {
        $this->isDebug = config('vodafone-name.testing-mode');
    }

    public function search(string $msisdn, string $language): ?string
    {
        return $this->isTesting ? $this->getTestingName() : $this->getName($msisdn, $language);
    }

    private function getName(string $msisdn, string $language): ?string
    {
        $token = resolve(CurlRequestService::class)->getToken();

        $nameResponse = resolve(CurlRequestService::class)->post("/customer/api/customerManagement/v3/customer/{$msisdn}?profile=NAME", [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json',
            "Accept-Language: $language"
        ]);

        try {

            return $nameResponse['characteristic'][0]['value'];

        } catch (Exception $exception) {

            return null;

        }
    }

    private function getTestingName(): string
    {
        $faker = Factory::create();

        return $faker->name;
    }
}
