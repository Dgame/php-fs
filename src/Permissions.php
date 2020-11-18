<?php

declare(strict_types=1);

namespace Dgame\Fs;

use InvalidArgumentException;

final class Permissions
{
    private int $mode;

    private function __construct(int $mode)
    {
        if ($mode < 0 || $mode > 0777) {
            throw new InvalidArgumentException($mode . ' is not a valid permission');
        }

        $this->mode = $mode;
    }

    public static function withInt(int $mode): self
    {
        return new self($mode);
    }

    public static function withOctal(string $mode): self
    {
        return new self((int) octdec($mode));
    }

    public static function for(Path $path): self
    {
        if (!$path->exists()) {
            return self::none();
        }

        $perm = fileperms((string) $path);
        if ($perm === false) {
            return self::none();
        }

        $mode = substr(sprintf('%o', $perm), -4);

        return self::withOctal($mode);
    }

    public static function none(): self
    {
        return new self(0);
    }

    public static function new(): PermissionBuilder
    {
        return new PermissionBuilder();
    }

    public function toInt(): int
    {
        return $this->mode;
    }

    public function inOctal(): int
    {
        return (int) decoct($this->mode);
    }

    /**
     * @return string
     */
    private function identifyFileType(): string
    {
        switch ($this->mode & 0xF000) {
            case 0xC000: // Socket
                return 's';
            case 0xA000: // Symbolischer Link
                return 'l';
            case 0x8000: // Regulär
                return 'r';
            case 0x6000: // Block special
                return 'b';
            case 0x4000: // Verzeichnis
                return 'd';
            case 0x2000: // Character special
                return 'c';
            case 0x1000: // FIFO pipe
                return 'p';
            default: // unbekannt
                return 'u';
        }
    }

    /**
     * @return string
     */
    private function getUserPermission(): string
    {
        $info = $this->mode & 0x0100 ? 'r' : '-';
        $info .= $this->mode & 0x0080 ? 'w' : '-';

        if ($this->mode & 0x0040) {
            return $info . ($this->mode & 0x0800 ? 's' : 'x');
        }

        return $info . ($this->mode & 0x0800 ? 'S' : '-');
    }

    /**
     * @return string
     */
    private function getGroupPermission(): string
    {
        $info = $this->mode & 0x0020 ? 'r' : '-';
        $info .= $this->mode & 0x0010 ? 'w' : '-';
        if ($this->mode & 0x0008) {
            return $info . ($this->mode & 0x0400 ? 's' : 'x');
        }

        return $info . ($this->mode & 0x0400 ? 'S' : '-');
    }

    /**
     * @return string
     */
    private function getOtherPermission(): string
    {
        $info = $this->mode & 0x0004 ? 'r' : '-';
        $info .= $this->mode & 0x0002 ? 'w' : '-';
        if ($this->mode & 0x0001) {
            return $info . ($this->mode & 0x0200 ? 't' : 'x');
        }

        return $info . ($this->mode & 0x0200 ? 'T' : '-');
    }

    public function __toString(): string
    {
        $info = $this->identifyFileType();
        $info .= $this->getUserPermission();
        $info .= $this->getGroupPermission();
        $info .= $this->getOtherPermission();

        return $info;
    }
}
