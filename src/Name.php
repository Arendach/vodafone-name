<?php

declare(strict_types=1);

namespace Arendach\VodafoneName;

use Arendach\VodafoneName\Services\GetName;
use Arendach\MultiSessions\Session;
use Psr\SimpleCache\InvalidArgumentException;

class Name
{
    /**
     * @var GetName
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
        $this->nameService = resolve(GetName::class);
        $this->cacheStorage = Session::instance('personification');

        $this->loading();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function loading(): void
    {
        $locale = currentLocale();

        $name = $this->cacheStorage->get("name_{$locale}");
        $nameStatus = $this->cacheStorage->get("name_status_{$locale}");

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
     * @param string|null $phone
     * @return string|null
     */
    public function searchAndSave(?string $phone): ?string
    {
        $name = !$phone ? null : $this->nameService->search($phone);

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

        $this->cacheStorage->set("name_{$locale}", $name);
        $this->cacheStorage->set("name_status_{$locale}", $name ? 1 : -1);
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
        $locale = request()->hasHeader('Content-Language')
            ? request()->header('Content-Language')
            : app()->getLocale();

        return $locale == 'ua' ? 'uk' : $locale;
    }

    public function rebootSession(): void
    {
        $locale = self::currentLocale();

        if ($this->cacheStorage->get("name_status_{$locale}") == -1) {
            $this->cacheStorage->set("name_status_{$locale}", 0);
        }
    }
}