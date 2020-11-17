<?php

declare(strict_types=1);

namespace Dgame\File;

final class ReadMode extends Mode
{
    private bool $write = false;

    public function withWrite(): self
    {
        $this->write = true;

        return $this;
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
