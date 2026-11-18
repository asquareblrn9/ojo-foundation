<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super-admin';
    case Administrator = 'administrator';
    case ContentEditor = 'content-editor';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Administrator => 'Administrator',
            self::ContentEditor => 'Content Editor',
        };
    }

    /**
     * Permissions granted to each role. Super Admin is also granted
     * everything implicitly via Gate::before in AppServiceProvider.
     *
     * @return list<Permission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::SuperAdmin => Permission::cases(),
            self::Administrator => array_values(array_filter(
                Permission::cases(),
                fn (Permission $p) => ! in_array($p, [Permission::ManageUsers, Permission::ViewActivityLog], true),
            )),
            self::ContentEditor => [
                Permission::AccessDashboard,
                Permission::ManagePosts,
                Permission::ManageProgrammes,
                Permission::ManageGallery,
                Permission::ManagePages,
            ],
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $r) => [$r->value => $r->label()])->all();
    }
}
