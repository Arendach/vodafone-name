<?php

declare(strict_types=1);

namespace Arendach\VodafoneName\Services;

use Arendach\VodafoneName\Name;
use Faker\Factory;
use Throwable;

class GetName
{
    private $isTesting;
    private $logger;

    public function __construct()
    {
        $this->isTesting = config('vodafone-name.testing-mode') || env('APP_ENV') == 'testing';
        $this->logger = resolve(Logger::class);
    }

    public function search(string $msisdn): ?string
    {
        return $this->isTesting ? $this->getTestingName() : $this->getName($msisdn);
    }

    private function getName(string $msisdn): ?string
    {
        try {

            $supportLocales = config('vodafone-name.support_languages');
            $locale = Name::currentLocale();

            if (!in_array($locale, $supportLocales)) {
                return null;
            }

            $token = resolve(CurlRequest::class)->getToken();
            $profile = config('vodafone-name.middleware-profile');
            $channel = config('vodafone-name.middleware-channel');

            if (!$token) {
                return null;
            }

            $nameResponse = resolve(CurlRequest::class)->get("/customer/api/customerManagement/v3/customer/{$msisdn}", [
                "Authorization: Bearer {$token}",
                'Content-Type: application/json',
                "Accept-Language: {$locale}",
                "Profile: {$profile}",
                "Channel: {$channel}"
            ]);


            return $nameResponse['characteristic'][0]['value'] ?? null;

        } catch (Throwable $exception) {

            $this->logger->save($exception);

            return null;

        }
    }

    private function getTestingName(): string
    {
        return Factory::create()->name;
    }
}
