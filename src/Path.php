<?php

declare(strict_types=1);

namespace Dgame\Fs;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Path
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getFilename(): string
    {
        return basename($this->path);
    }

    public function getParent(): self
    {
        return new self(dirname($this->path));
    }

    public function isLink(): bool
    {
        return is_link($this->path);
    }

    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    public function isExecutable(): bool
    {
        return is_executable($this->path);
    }

    public function getPathInfo(): Pathinfo
    {
        return new Pathinfo($this);
    }

    public function rename(string $newname): bool
    {
        return rename($this->path, $newname);
    }

    public function isDirectory(): bool
    {
        return is_dir($this->path);
    }

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function isFile(): bool
    {
        return is_file($this->path);
    }

    public function deleteAll(): bool
    {
        if ($this->isDirectory()) {
            return $this->cleanContent() && $this->delete();
        }

        return $this->delete();
    }

    public function cleanContent(): bool
    {
        if (!$this->exists()) {
            return true;
        }

        if ($this->isFile()) {
            return file_put_contents($this->path, '') !== false;
        }

        $rit = new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);
        $rit = new RecursiveIteratorIterator($rit, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($rit as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }

        return true;
    }

    public function delete(): bool
    {
        if (!$this->exists()) {
            return true;
        }

        if ($this->isFile()) {
            return unlink($this->path);
        }

        return rmdir($this->path);
    }

    public function create(Permissions $permissions = null): bool
    {
        if ($this->exists()) {
            return true;
        }

        if ($this->isFile()) {
            if (!file_put_contents($this->path, '') !== false) {
                return false;
            }

            return $permissions === null ? true : $this->changePermissionsTo($permissions);
        }

        if ($permissions === null) {
            return mkdir($this->path, 0777, true);
        }

        return mkdir($this->path, $permissions->inOctal(), true);
    }

    public function changePermissionsTo(Permissions $permissions): bool
    {
        return chmod($this->path, $permissions->inOctal());
    }

    public function getPermissions(): Permissions
    {
        return Permissions::for($this);
    }

    public function __toString(): string
    {
        return $this->path;
    }
}
