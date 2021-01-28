<?php

declare(strict_types=1);

namespace Arendach\VodafoneName\Services;

use Arendach\VodafoneName\Name;
use Faker\Factory;
use Exception;

class GetNameService
{
    private $isTesting;

    public function __construct()
    {
        $this->isTesting = config('vodafone-name.testing-mode');
    }

    public function search(string $msisdn): ?string
    {
        return $this->isTesting ? $this->getTestingName() : $this->getName($msisdn);
    }

    private function getName(string $msisdn): ?string
    {
        $locale = Name::currentLocale();
        $token = resolve(CurlRequestService::class)->getToken();
        $profile = config('vodafone-name.middleware-profile');
        $channel = config('vodafone-name.middleware-channel');

        if (!$token) {
            return null;
        }

        $nameResponse = resolve(CurlRequestService::class)->get("/customer/api/customerManagement/v3/customer/{$msisdn}", [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json',
            "Accept-Language: {$locale}",
            "Profile: {$profile}",
            "Channel: {$channel}"
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
