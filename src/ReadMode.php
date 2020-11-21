<?php

declare(strict_types=1);

namespace Dgame\Fs;

final class ReadMode extends Mode
{
    use BinaryMode;

    private bool $write = false;

    public function withWrite(): self
    {
        $mode = clone $this;
        $mode->write = true;

        return $mode;
    }

    public function __toString(): string
    {
        $mode = 'r';
        if ($this->binary) {
            $mode .= 'b';
        }

        if ($this->write) {
            $mode .= '+';
        }

        return $mode;
    }
}
