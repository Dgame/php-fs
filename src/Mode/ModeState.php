<?php

declare(strict_types=1);

namespace Dgame\File\Mode;

use Dgame\File\Mode;

interface ModeState
{
    public function with(string $letter): void;

    public function intoMode(): Mode;
}
