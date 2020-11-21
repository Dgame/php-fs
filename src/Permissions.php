<?php

declare(strict_types=1);

namespace Dgame\Fs;

use InvalidArgumentException;
use UnexpectedValueException;

use function Symfony\Component\String\s;

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

    public static function fromHumanReadable(string $permission): self
    {
        $str = s($permission);
        $len = $str->length();
        if ($len < 9 || $len > 10) {
            throw new UnexpectedValueException(
                'A permission as human readable string should be between 9 and 10 characters long'
            );
        }

        if ($len > 9) {
            $str = $str->slice(1);
        }

        [$user, $group, $other] = $str->chunk(3);

        $user = Permission::fromHumanReadable($user->toString());
        $group = Permission::fromHumanReadable($group->toString());
        $other = Permission::fromHumanReadable($other->toString());

        $mode = $user->toInt() . $group->toInt() . $other->toInt();

        return self::withOctal($mode);
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

        $mode = s(sprintf('%o', $perm))->slice(-4);

        return self::withOctal($mode->toString());
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

    public function getUserPermission(): Permission
    {
        return Permission::fromHumanReadable($this->getUserPermissionAsString());
    }

    public function getGroupPermission(): Permission
    {
        return Permission::fromHumanReadable($this->getGroupPermissionAsString());
    }

    public function getOtherPermission(): Permission
    {
        return Permission::fromHumanReadable($this->getOtherPermissionAsString());
    }

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

    private function getUserPermissionAsString(): string
    {
        $info = $this->mode & 0x0100 ? 'r' : '-';
        $info .= $this->mode & 0x0080 ? 'w' : '-';

        if ($this->mode & 0x0040) {
            return $info . ($this->mode & 0x0800 ? 's' : 'x');
        }

        return $info . ($this->mode & 0x0800 ? 'S' : '-');
    }

    private function getGroupPermissionAsString(): string
    {
        $info = $this->mode & 0x0020 ? 'r' : '-';
        $info .= $this->mode & 0x0010 ? 'w' : '-';
        if ($this->mode & 0x0008) {
            return $info . ($this->mode & 0x0400 ? 's' : 'x');
        }

        return $info . ($this->mode & 0x0400 ? 'S' : '-');
    }

    private function getOtherPermissionAsString(): string
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
        $info .= $this->getUserPermissionAsString();
        $info .= $this->getGroupPermissionAsString();
        $info .= $this->getOtherPermissionAsString();

        return $info;
    }
}
