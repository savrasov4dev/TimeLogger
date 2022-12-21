<?php

declare(strict_types=1);

class TimeLogger
{
    /**
     * Файл и путь до него.
     *
     * @example '/Path/To/Filename.extension'
     * @var string
     */
    private $file;

    /**
     * Время последней записи в лог в файл.
     *
     * @var float
     */
    private $lastTime;

    /**
     * Хранилище промежутков времени между вызовами записи в логфайл.
     *
     * @var array
     */
    private $timeStorage = [];

    /**
     * Индекс текущей/последней записи в логфайл.
     *
     * @var int
     */
    private $currentIndex = 0;

    /**
     * Принимает строкеу, в которой указан файл и путь до него, и устанавливает текущее время.
     *
     * @param string $file
     * @example $file = '/Path/To/Filename.extension';
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->lastTime = microtime(true);
    }

    /**
     * Делает запись в установленный логфайл, в которой есть:
     *   1. Индекс записи;
     *   2. Разница во времени между текущей записью и предыдущей.
     *      Если текущая запись первая, то разница ищется между ней и собзданием объекта.
     * При необходимости 3-им пунктом может быть запись сообщения. По умолчанию сообщение не добавляется.
     *
     * Возвращает индекс текущей записи.
     *
     * @param string $msg
     * @return int
     */
    public function log(string $msg = ''): int
    {
        $time = microtime(true) - $this->lastTime;
        $this->timeStorage[] = $time;
        $this->lastTime = microtime(true);

        $note = 'TL : <' . $this->currentIndex++ . '> | time: ' . number_format($time, 6, '.', ' ');

        if ($msg) $note .= ' | ' . $msg;

        $note .= PHP_EOL;

        file_put_contents($this->file, $note, FILE_APPEND);

        return $this->currentIndex;
    }

    /** Очищает логфайл и сбрасывает текущее состояние объекта. */
    public function clear(): void
    {
        file_put_contents($this->file, '');
        $this->currentIndex = 0;
        $this->timeStorage = [];
        $this->lastTime = microtime(true);
    }

    /**
     * По умолчанию находит сумму промежутков времени от первой до последней записи.
     * При необходимости можно передать от какой точки начинать суммировать промежутки времени.
     *
     * @param int $fromIndex
     * @return float
     */
    public function findTimeSum(int $fromIndex = 0): float
    {
        if ($fromIndex > $this->currentIndex) $fromIndex = $this->currentIndex;

        for ($i = $fromIndex, $timeSum = 0; $i < count($this->timeStorage); $i++) $timeSum += $this->timeStorage[$i];

        return $timeSum;
    }
}
