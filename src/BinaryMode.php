<?php

declare(strict_types=1);

namespace Dgame\Fs;

trait BinaryMode
{
    private bool $binary = false;

    public function inBinary(): self
    {
        $mode = clone $this;
        $mode->binary = true;

        return $mode;
    }
}
