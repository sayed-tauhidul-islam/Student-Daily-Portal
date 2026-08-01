<?php

namespace App\Support;

use App\Models\Complaint;
use App\Models\NotificationRead;
use App\Models\Notice;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;

class PortalNotificationFeed
{
    public static function forUser(?User $user, int $limit = 20): Collection
    {
        if (! $user) {
            return collect();
        }

        $school = self::currentSchool($user);
        $userId = (string) $user->getKey();
        $role = (string) ($user->role ?? '');
        $items = collect();

        Notice::query()->get()
            ->filter(fn (Notice $notice) => self::sameInstitute((string) ($notice->institute ?? ''), $school))
            ->filter(fn (Notice $notice) => (string) ($notice->teacher_user_id ?? '') !== $userId)
            ->each(function (Notice $notice) use ($items, $role) {
                $items->push([
                    'type' => 'notice',
                    'item_type' => 'notice',
                    'item_id' => (string) $notice->getKey(),
                    'title' => (string) ($notice->title ?? 'Notice'),
                    'body' => (string) ($notice->body ?? ''),
                    'created_at' => $notice->published_at ?? $notice->created_at,
                    'url' => self::noticeUrl($role),
                ]);
            });

        if ($role === 'teacher_admin') {
            Complaint::query()
                ->where('school', $school)
                ->get()
                ->filter(fn (Complaint $complaint) => (string) ($complaint->created_by ?? '') !== $userId)
                ->each(function (Complaint $complaint) use ($items) {
                    $items->push([
                        'type' => 'complaint',
                        'item_type' => 'complaint',
                        'item_id' => (string) $complaint->getKey(),
                        'title' => 'Complaint: '.(string) ($complaint->title ?? 'Untitled'),
                        'body' => (string) ($complaint->body ?? ''),
                        'created_at' => $complaint->created_at,
                        'url' => route('teacher-admin.complaints'),
                    ]);
                });
        }

        $reads = NotificationRead::query()
            ->where('user_id', $userId)
            ->whereIn('item_id', $items->pluck('item_id')->values()->all())
            ->get()
            ->mapWithKeys(fn (NotificationRead $read) => [$read->item_type.':'.$read->item_id => $read]);

        return $items
            ->map(function (array $item) use ($reads) {
                $key = $item['item_type'].':'.$item['item_id'];
                $item['seen'] = $reads->has($key);

                return $item;
            })
            ->sortByDesc(fn (array $item) => (string) ($item['created_at'] ?? ''))
            ->take($limit)
            ->values();
    }

    public static function markSeen(?User $user, Collection $items): void
    {
        if (! $user) {
            return;
        }

        $userId = (string) $user->getKey();
        $items->each(function (array $item) use ($userId) {
            NotificationRead::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'],
                ],
                ['seen_at' => now()]
            );
        });
    }

    public static function unreadCount(?User $user): int
    {
        return self::forUser($user, 100)->where('seen', false)->count();
    }

    private static function currentSchool(User $user): string
    {
        return match ($user->role) {
            'student' => (string) (Student::query()->firstWhere('user_id', $user->getKey())?->school ?? $user->school ?? ''),
            'teacher' => (string) (Teacher::query()->firstWhere('user_id', $user->getKey())?->institution ?? $user->school ?? ''),
            default => (string) ($user->school ?? ''),
        };
    }

    private static function noticeUrl(string $role): string
    {
        return match ($role) {
            'teacher_admin' => route('teacher-admin.notices.index'),
            'teacher' => route('teacher.notices.index'),
            default => route('student.notices.index'),
        };
    }

    private static function sameInstitute(string $left, string $right): bool
    {
        $left = self::normalize($left);
        $right = self::normalize($right);

        return $left !== '' && $right !== '' && $left === $right;
    }

    private static function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace('&', ' and ', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
