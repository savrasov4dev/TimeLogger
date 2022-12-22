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
     * Хранилище времени каждой сделанной записи в логфайл.
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
     * Принимает строку, в которой указан файл и путь до него, и устанавливает текущее время.
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
     *   2. Разница во времени между текущей записью и предыдущей в секундах.
     *      Если текущая запись первая, то разница ищется между ней и собзданием объекта.
     *
     * При необходимости 3-им пунктом может быть текст сообщения. По умолчанию сообщение не добавляется.
     *
     * Возвращает индекс текущей записи.
     *
     * @param string $msg
     * @return int
     */
    public function log(string $msg = ''): int
    {
        $time = microtime(true) - $this->lastTime;
        $this->timeStorage[]
            = $this->lastTime
            = microtime(true);

        $note = 'TL : <' . $this->currentIndex . '> | time: ' . number_format($time, 6, '.', ' ') . ' sec';

        if ($msg) $note .= ' | ' . $msg;

        $note .= PHP_EOL;

        file_put_contents($this->file, $note, FILE_APPEND);

        return $this->currentIndex++;
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
     * По умолчанию находит время между первой и последней записью в логфайл.
     * При необходимости можно найти интервал между произвольно выбранных индексов среди существующих записей
     * и, если последний параметр равен true, записать его в логфайл.
     *
     * @param int $from
     * @param int $to
     * @param bool $record
     * @return float
     */
    public function findInterval(int $from = 0, int $to = 0, bool $record = false): float
    {
        foreach ([&$from, &$to] as &$i) if ($i > ($this->currentIndex - 1)) $i = ($this->currentIndex - 1);

        $result = abs($this->timeStorage[$to] - $this->timeStorage[$from]);

        if ($record) file_put_contents(
            $this->file,
            ('TL : interval <' . $from . '> - <' . $to . '> | time: ' . number_format($result, 6, '.', ' ') . ' sec' . PHP_EOL),
            FILE_APPEND
        );

        return $result;
    }
}
