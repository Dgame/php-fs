<?php

declare(strict_types=1);

namespace Dgame\Fs\Mode;

use Dgame\Fs\Mode;
use UnexpectedValueException;

final class WriteModeState implements ModeState
{
    private const ALLOWED = ['+', 'b'];

    private bool $binary = false;
    private bool $read = false;

    public function with(string $letter): void
    {
        switch ($letter) {
            case '+':
                $this->read = true;
                break;
            case 'b':
                $this->binary = true;
                break;
            default:
                throw new UnexpectedValueException(
                    'Expected one of "' . implode(', ', self::ALLOWED) . '" not ' . $letter
                );
        }
    }

    public function isAllowed(string $letter): bool
    {
        return in_array($letter, self::ALLOWED, true);
    }

    /**
     * @return Mode
     */
    public function intoMode(): Mode
    {
        $mode = Mode::write();
        if ($this->binary) {
            $mode->inBinary();
        }

        if ($this->read) {
            $mode->withRead();
        }

        return $mode;
    }
}
