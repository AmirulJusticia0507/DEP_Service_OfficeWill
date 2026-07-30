<?php

namespace App\Helpers;

use App\Models\Employee;
use App\Models\Notification;

class NotificationHelper
{
    public static function send(Employee $employee, string $type, string $title, string $message, ?string $link = null): Notification
    {
        return Notification::create([
            'employee_id' => $employee->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    public static function sendToMany($employees, string $type, string $title, string $message, ?string $link = null): void
    {
        foreach ($employees as $employee) {
            self::send($employee, $type, $title, $message, $link);
        }
    }
}
