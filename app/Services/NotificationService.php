<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\Role;
use Illuminate\Support\Arr;

class NotificationService
{
    private const FINANCIAL_TYPES = [
        'quotation_submitted', 'quotation_approved', 'invoice_due',
    ];

    public static function pushToUser(string $type, array $data, int $userId): Notification
    {
        return Notification::create([
            'type' => $type,
            'data' => self::sanitize($data),
            'user_id' => $userId,
        ]);
    }

    public static function pushToRolesByName(string $type, array $data, array $roleNames): ?Notification
    {
        $roleIds = Role::query()->whereIn('name', $roleNames)->pluck('id')->all();

        return self::pushToRoleIds($type, $data, $roleIds);
    }

    public static function pushToRoleIds(string $type, array $data, array $roleIds): ?Notification
    {
        $roleIds = array_values(array_filter(array_map('intval', $roleIds)));
        if (in_array($type, self::FINANCIAL_TYPES, true)) {
            $roleIds = Role::query()->whereIn('id', $roleIds)
                ->whereNotIn('name', ['mandor', 'user'])->pluck('id')->all();
        }
        if (empty($roleIds)) {
            return null;
        }

        $n = Notification::create([
            'type' => $type,
            'data' => self::sanitize($data),
        ]);

        $n->roles()->syncWithoutDetaching($roleIds);

        return $n;
    }

    public static function isFinancialType(string $type): bool
    {
        return in_array($type, self::FINANCIAL_TYPES, true);
    }

    public static function pushToAllowedRoles(string $type, array $data): ?Notification
    {
        $roleIds = NotificationPreference::query()
            ->where('type', $type)
            ->where('allowed', 1)
            ->pluck('role_id')
            ->all();

        return self::pushToRoleIds($type, $data, $roleIds);
    }

    protected static function sanitize(array $data): array
    {
        return [
            'title' => Arr::get($data, 'title'),
            'message' => Arr::get($data, 'message'),
            'link' => Arr::get($data, 'link'),
            'icon' => Arr::get($data, 'icon', 'bell'),
        ];
    }
}
