<?php

declare(strict_types=1);

namespace Arendach\VodafoneName\Services;

use Log;
use Exception;
use Throwable;
use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * Logger constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->log = Log::channel('name');
        $this->isDebug = config('vodafone-name.debug-mode');
    }

    /**
     * @param string|Throwable $message
     */
    public function save($message): void
    {
        if (!$this->isDebug) {
            return;
        }

        if ($message instanceof Throwable) {
            $message = $this->getMessageFromObject($message);
        }

        $this->log->info($message);
    }

    private function getMessageFromObject(Throwable $exception): string
    {
        return $exception->getMessage() . PHP_EOL . $exception->getFile() . PHP_EOL . $exception->getTraceAsString();
    }
}