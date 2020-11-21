<?php

declare(strict_types=1);

namespace Dgame\Fs;

use UnexpectedValueException;

use function Symfony\Component\String\s;

final class Permission
{
    private bool $read;
    private bool $write;
    private bool $execute;

    private function __construct(bool $read, bool $write, bool $execute)
    {
        $this->read = $read;
        $this->write = $write;
        $this->execute = $execute;
    }

    public static function fromHumanReadable(string $permission): self
    {
        $str = s($permission);
        if ($str->length() !== 3) {
            throw new UnexpectedValueException(
                'A permission as human readable string should be exactly 3 characters long, e.g. "rwx"'
            );
        }

        [$read, $write, $execute] = $str->chunk();

        return new self($read->equalsTo('r'), $write->equalsTo('w'), $execute->equalsTo('x'));
    }

    public static function readWriteExecute(): self
    {
        return new self(true, true, true);
    }

    public static function readWrite(): self
    {
        return new self(true, true, false);
    }

    public static function readExecute(): self
    {
        return new self(true, false, true);
    }

    public static function readOnly(): self
    {
        return new self(true, false, false);
    }

    public static function writeExecute(): self
    {
        return new self(false, true, true);
    }

    public static function writeOnly(): self
    {
        return new self(false, true, false);
    }

    public static function executeOnly(): self
    {
        return new self(false, false, true);
    }

    public static function none(): self
    {
        return new self(false, false, false);
    }

    public function isReadAllowed(): bool
    {
        return $this->read;
    }

    public function isWriteAllowed(): bool
    {
        return $this->write;
    }

    public function isExecuteAllowed(): bool
    {
        return $this->execute;
    }

    public function toInt(): int
    {
        return (int) bindec($this->inBinary());
    }

    public function inBinary(): string
    {
        return ($this->read ? 1 : 0) . ($this->write ? 1 : 0) . ($this->execute ? 1 : 0);
    }

    public function __toString(): string
    {
        return ($this->read ? 'r' : '-') . ($this->write ? 'w' : '-') . ($this->execute ? 'x' : '-');
    }
}
