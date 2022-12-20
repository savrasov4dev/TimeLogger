<?php

declare(strict_types=1);

class TimeLogger
{
    private $file;
    private $lastTime;
    private $timeSum = [];
    private $currentPoint = 0;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->lastTime = microtime(true);
    }

    public function log(string $msg = ''): float
    {
        $time = microtime(true) - $this->lastTime;
        $this->timeSum[] = $time;
        $this->lastTime = microtime(true);

        $note = 'TL : <' . $this->currentPoint++ . '> | time: ' . number_format($time, 6, '.', ' ');

        if ($msg) $note .= ' | ' . $msg;

        $note .= PHP_EOL;

        file_put_contents($this->file, $note, FILE_APPEND);

        return $this->currentPoint;
    }

    public function clear(): void
    {
        file_put_contents($this->file, '');
        $this->currentPoint = 0;
        $this->timeSum = [];
        $this->lastTime = microtime(true);
    }

    public function findTimeSum(int $fromPoint = 0): float
    {
        if ($fromPoint > $this->currentPoint) $fromPoint = $this->currentPoint;

        for ($i = $fromPoint, $timeSum = 0; $i < count($this->timeSum); $i++) $timeSum += $this->timeSum[$i];

        return $timeSum;
    }
}
