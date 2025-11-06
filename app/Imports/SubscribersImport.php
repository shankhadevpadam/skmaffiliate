<?php

namespace App\Imports;

use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\ImportHasFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ImportFailed;

class SubscribersImport implements ShouldQueue, ToCollection, WithChunkReading, WithEvents
{
    public function __construct(public User $user) {}

    public function collection(Collection $rows): void
    {
        $data = $rows->map(function ($row) {
            return [
                'user_id' => $this->user->id,
                'first_name' => $row[0],
                'last_name' => $row[1],
                'email' => $row[2],
                'phone' => $row[3] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        Subscriber::upsert(
            $data,
            ['user_id', 'email'],
            ['first_name', 'last_name', 'phone', 'updated_at']
        );
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event) {
                $this->user->notify(new ImportHasFailedNotification);
            },
        ];
    }
}
