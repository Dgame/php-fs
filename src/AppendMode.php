<?php

declare(strict_types=1);

namespace Dgame\Fs;

final class AppendMode extends Mode
{
    private bool $read = false;

    public function withRead(): self
    {
        $mode = clone $this;
        $mode->read = true;

        return $mode;
    }

    public function __toString(): string
    {
        $mode = 'a';
        if ($this->binary) {
            $mode .= 'b';
        }

        if ($this->read) {
            $mode .= '+';
        }

        return $mode;
    }
}
