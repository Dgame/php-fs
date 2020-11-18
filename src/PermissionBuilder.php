<?php

declare(strict_types=1);

namespace Dgame\Fs;

final class PermissionBuilder
{
    private Permission $user;
    private Permission $group;
    private Permission $other;

    public function __construct()
    {
        $this->user = $this->group = $this->other = Permission::none();
    }

    public function forUser(Permission $permission): self
    {
        $this->user = $permission;

        return $this;
    }

    public function forGroup(Permission $permission): self
    {
        $this->group = $permission;

        return $this;
    }

    public function forOther(Permission $permission): self
    {
        $this->other = $permission;

        return $this;
    }

    public function build(): Permissions
    {
        $mode = $this->user->toInt() . $this->group->toInt() . $this->other->toInt();

        return Permissions::withOctal($mode);
    }
}
