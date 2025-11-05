<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Subscriber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Notifications\ImportHasFailedNotification;

class SubscribersImport implements ToModel, WithChunkReading, ShouldQueue, WithEvents
{
    public function __construct(public User $user) {}

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Subscriber([
            'user_id' => $this->user->id,
            'first_name' => $row[0],
            'last_name' => $row[1],
            'email' => $row[2],
            'phone' => $row[3] ?? null,
        ]);
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
