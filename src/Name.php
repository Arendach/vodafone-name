<?php

declare(strict_types=1);

namespace Arendach\VodafoneName;

use Arendach\VodafoneName\Services\GetNameService;
use Arendach\MultiSessions\Session;
use Psr\SimpleCache\InvalidArgumentException;

class Name
{
    /**
     * @var GetNameService
     */
    private $nameService;

    /**
     * @var Session
     */
    private $cacheStorage;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int
     */
    private $nameStatus;

    /**
     * Name constructor.
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->nameService = resolve(GetNameService::class);

        $this->loading();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function loading(): void
    {
        $locale = currentLocale();

        $name = $this->getCacheStorage()->get("name_{$locale}");
        $nameStatus = $this->getCacheStorage()->get("name_status_{$locale}");

        $this->name = $name;
        $this->nameStatus = in_array($nameStatus, [1, -1]) ? $nameStatus : 0;
    }

    /**
     * @param string $phone
     * @return string|null
     */
    public function search(string $phone): ?string
    {
        return $this->nameService->search($phone);
    }

    /**
     * @param string $phone
     * @return string|null
     */
    public function searchAndSave(string $phone): ?string
    {
        $name = $this->nameService->search($phone);

        $this->name = $name;
        $this->nameStatus = $name ? 1 : -1;

        $this->saveToCache($name);

        return $this->name;
    }

    /**
     * @param string|null $name
     * @param string|null $locale
     */
    private function saveToCache(?string $name, string $locale = null): void
    {
        if (is_null($locale)) {
            $locale = currentLocale();
        }

        $this->getCacheStorage()->set("name_{$locale}", $name);
        $this->getCacheStorage()->set("name_status_{$locale}", $name ? 1 : -1);
    }

    /**
     * @return Session
     */
    private function getCacheStorage(): Session
    {
        $abstract = Session::abstractKey('personification');

        if (!$this->cacheStorage) {
            $this->cacheStorage = app($abstract);
        }

        return $this->cacheStorage;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->nameStatus;
    }

    /**
     * @return string
     */
    public static function currentLocale(): string
    {
        $locale = app()->getLocale();

        return $locale == 'ua' ? 'uk' : $locale;
    }
}