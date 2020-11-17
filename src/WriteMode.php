<?php

declare(strict_types=1);

namespace Dgame\File;

final class WriteMode extends Mode
{
    private bool $read = false;

    public function withRead(): self
    {
        $this->read = true;

        return $this;
    }

    public function __toString(): string
    {
        $mode = 'w';
        if ($this->binary) {
            $mode .= 'b';
        }

        if ($this->read) {
            $mode .= '+';
        }

        return $mode;
    }
}
