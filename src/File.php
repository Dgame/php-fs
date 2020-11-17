<?php

declare(strict_types=1);

namespace Dgame\File;

use InvalidArgumentException;
use UnexpectedValueException;

final class File
{
    /** @var resource */
    private $handle;

    /**
     * @param resource $handle
     *
     * @psalm-suppress DocblockTypeContradiction
     */
    private function __construct($handle)
    {
        if (!is_resource($handle)) {
            throw new UnexpectedValueException('Expected resource');
        }

        $this->handle = $handle;
    }

    /**
     * @param resource $handle
     *
     * @return File
     */
    public static function from($handle): File
    {
        return new self($handle);
    }

    public static function open(string $filename, Mode $mode): self
    {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException($filename . ' is not an existing file');
        }

        $handle = fopen($filename, (string) $mode);
        if ($handle === false) {
            throw new UnexpectedValueException('Could not open file ' . $filename);
        }

        return new self($handle);
    }

    public static function temp(Mode $mode): self
    {
        $handle = fopen('php://temp', (string) $mode);
        if ($handle === false) {
            throw new UnexpectedValueException('Could not open temp-stream');
        }

        return new self($handle);
    }

    public static function memory(Mode $mode): self
    {
        $handle = fopen('php://memory', (string) $mode);
        if ($handle === false) {
            throw new UnexpectedValueException('Could not open memory-stream');
        }

        return new self($handle);
    }

    public static function stdIn(): self
    {
        $handle = fopen('php://stdin', 'rb');
        if ($handle === false) {
            throw new UnexpectedValueException('Could not open stdin-stream');
        }

        return new self($handle);
    }

    public static function stdOut(): self
    {
        $handle = fopen('php://stdout', 'wb');
        if ($handle === false) {
            throw new UnexpectedValueException('Could not open stdout-stream');
        }

        return new self($handle);
    }

    public static function stdErr(): self
    {
        $handle = fopen('php://stderr', 'wb+');
        if ($handle === false) {
            throw new UnexpectedValueException('Could not open stderr-stream');
        }

        return new self($handle);
    }

    public function __destruct()
    {
        fclose($this->handle);
    }

    public function getContentLength(): int
    {
        $offset = $this->getWriteOffset();
        $this->setWriteOffsetToEnd();

        try {
            return $this->getWriteOffset();
        } finally {
            $this->setWriteOffsetToStart($offset);
        }
    }

    public function getWriteOffset(): int
    {
        $result = ftell($this->handle);
        if ($result === false) {
            throw new UnexpectedValueException('Could not get the write position');
        }

        return $result;
    }

    public function setWriteOffsetToEnd(int $offset = 0): void
    {
        $result = fseek($this->handle, $offset, SEEK_END);
        if ($result === -1) {
            throw new UnexpectedValueException('Could not set the write position to end');
        }
    }

    public function setWriteOffsetToStart(int $offset = 0): void
    {
        $result = fseek($this->handle, $offset, SEEK_SET);
        if ($result === -1) {
            throw new UnexpectedValueException('Could not set the write position to start');
        }
    }

    public function readFromStart(int $length): string
    {
        $offset = $this->getWriteOffset();
        $this->setWriteOffsetToStart();

        try {
            return $this->readFromCurrentOffset($length);
        } finally {
            $this->setWriteOffsetToStart($offset);
        }
    }

    public function readFromCurrentOffset(int $length): string
    {
        $result = fread($this->handle, $length);
        if ($result === false) {
            throw new UnexpectedValueException('Could not read from file');
        }

        return $result;
    }

    public function readLineFromStart(int $length = null): string
    {
        $offset = $this->getWriteOffset();
        $this->setWriteOffsetToStart();

        try {
            return $this->readLineFromCurrentOffset($length);
        } finally {
            $this->setWriteOffsetToStart($offset);
        }
    }

    public function readLineFromCurrentOffset(int $length = null): string
    {
        $result = $length === null ? fgets($this->handle) : fgets($this->handle, $length);
        if ($result === false) {
            throw new UnexpectedValueException('Could not read line');
        }

        return $result;
    }

    public function isCurrentOffsetAtEnd(): bool
    {
        return feof($this->handle);
    }

    public function setWriteOffsetToCurrentPosition(int $offset): void
    {
        $result = fseek($this->handle, $offset, SEEK_CUR);
        if ($result === -1) {
            throw new UnexpectedValueException('Could not set the write position to ' . $offset);
        }
    }

    public function write(string $content): void
    {
        $result = fwrite($this->handle, $content);
        if ($result === false) {
            throw new UnexpectedValueException('Could not write into file');
        }
    }

    public function truncateContentTo(int $length): void
    {
        $result = ftruncate($this->handle, $length);
        if ($result === false) {
            throw new UnexpectedValueException('Could not truncate to ' . $length);
        }
    }

    public function lockExclusive(): bool
    {
        $result = flock($this->handle, LOCK_EX, $wouldBlock);
        if ($result === false && $wouldBlock !== 1) {
            throw new UnexpectedValueException('Could not lock file');
        }

        return $result;
    }

    public function lockShared(): bool
    {
        $result = flock($this->handle, LOCK_SH, $wouldBlock);
        if ($result === false && $wouldBlock !== 1) {
            throw new UnexpectedValueException('Could not lock file');
        }

        return $result;
    }

    public function unlock(): bool
    {
        $result = flock($this->handle, LOCK_UN, $wouldBlock);
        if ($result === false && $wouldBlock !== 1) {
            throw new UnexpectedValueException('Could not unlock file');
        }

        return $result;
    }
}
