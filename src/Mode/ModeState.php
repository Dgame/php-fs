<?php

declare(strict_types=1);

namespace Dgame\Fs\Mode;

use Dgame\Fs\Mode;

interface ModeState
{
    public function with(string $letter): void;

    public function intoMode(): Mode;
}
