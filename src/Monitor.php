<?php

namespace Mitoop;

use Closure;

class Monitor
{
    use Macroable;

    protected $shouldQuit = false;

    protected $maxTime = 0;

    protected $maxJobs = 0;

    public function __construct(int $maxTime = 0, int $maxJobs = 0, array $signals = [])
    {
        $this->maxTime = $maxTime;

        $this->maxJobs = $maxJobs;

        if ($this->supportsAsyncSignals()) {
            $this->listenForSignals($signals);
        }
    }

    protected function supportsAsyncSignals(): bool
    {
        return extension_loaded('pcntl');
    }

    protected function signals(): array
    {
        return [SIGQUIT, SIGTERM, SIGHUP, SIGINT];
    }

    protected function listenForSignals(array $signals = []): void
    {
        pcntl_async_signals(true);

        $signals = $signals ?: $this->signals();

        foreach ($signals as $signal) {
            pcntl_signal($signal, function ($signal) {
                $this->shouldQuit = true;
            });
        }
    }

    protected function queueShouldRestart($lastRestart): bool
    {
        if (static::hasMacro('getTimestampOfLastQueueRestart')) {
            return $lastRestart !== (int) $this->getTimestampOfLastQueueRestart();
        }

        return false;
    }

    public function stopIfNecessary(int $startTime = 0, int $jobsProcessed = 0, int $lastRestart = 0, Closure $stoppingCallback = null): void
    {
        switch (true) {
            case $this->shouldQuit:
            case $this->maxTime > 0 && time() - $startTime >= $this->maxTime:
            case $this->maxJobs > 0 && $jobsProcessed >= $this->maxJobs:
            case $this->queueShouldRestart($lastRestart):
                if ($stoppingCallback) {
                    echo $stoppingCallback();
                    echo "\n";
                }

                posix_kill(getmypid(), SIGKILL);
        }
    }
}
