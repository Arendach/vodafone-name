<?php

declare(strict_types=1);

namespace Vodafone\Name;

use Vodafone\Name\Exceptions\GetNameException;
use Vodafone\Name\Services\GetNameService;
use MultiSessions\Session;

class Name
{
    /**
     * @var GetNameService
     */
    private $nameService;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var bool
     */
    private $useCache = true;

    /**
     * Name constructor.
     */
    public function __construct()
    {
        $this->config = config('vodafone-name');
        $this->nameService = resolve(GetNameService::class);
    }

    /**
     * @param bool $useCache
     * @return $this
     */
    public function useCache(bool $useCache = true): self
    {
        $this->useCache = $useCache;

        return $this;
    }

    /**
     * @param string $phone
     * @param string $language
     * @return string|null
     * @throws GetNameException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $phone, string $language = 'uk'): ?string
    {
        if (!$this->useCache) {
            return $this->nameService->search($phone, $language);
        }

        $this->saveToCache($phone);

        return $this->getSession()->get("name_{$language}");
    }

    /**
     * @param string $lang
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function hasCachedName(string $lang): bool
    {
        return $this->getSession()->has("name_{$lang}") && !is_null($this->getSession()->get("name_{$lang}"));
    }

    private function saveToCache(string $phone): void
    {
        foreach ($this->config['support-languages'] as $lang) {
            $name = $this->nameService->search($phone, $lang);

            $this->getSession()->set("name_{$lang}", $name);
        }
    }

    /**
     * @return Session
     */
    private function getSession(): Session
    {
        if (!$this->session) {
            $this->session = new Session('personification');
        }

        return $this->session;
    }
}