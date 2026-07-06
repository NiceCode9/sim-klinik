<?php

namespace App\Services\Registration;

use App\Models\Queue;
use App\Models\Specialization;

class QueueService
{
    public function generateQueueNumber(int $specializationId, string $source = 'offline'): int
    {
        $today = now()->startOfDay();

        $lastQueue = Queue::where('specialization_id', $specializationId)
            ->whereDate('created_at', $today)
            ->orderBy('queue_number', 'desc')
            ->first();

        if ($source === 'online') {
            $lastOnline = Queue::where('specialization_id', $specializationId)
                ->whereDate('created_at', $today)
                ->where('source', 'online')
                ->orderBy('queue_number', 'desc')
                ->first();

            if ($lastOnline) {
                return $lastOnline->queue_number + 1;
            }

            $lastOffline = Queue::where('specialization_id', $specializationId)
                ->whereDate('created_at', $today)
                ->where('source', 'offline')
                ->orderBy('queue_number', 'desc')
                ->first();

            return ($lastOffline ? $lastOffline->queue_number : 0) + 1;
        }

        return ($lastQueue ? $lastQueue->queue_number : 0) + 1;
    }

    public function getNextPatient(Specialization $specialization): ?Queue
    {
        $waiting = Queue::where('specialization_id', $specialization->id)
            ->whereIn('status', ['waiting', 'waiting_online_confirmation'])
            ->orderBy('queue_number')
            ->get();

        foreach ($waiting as $queue) {
            if ($queue->source === 'online' && $queue->checked_in_at === null) {
                continue;
            }

            return $queue;
        }

        return null;
    }

    public function getQueueForSpecialization(Specialization $specialization): array
    {
        $today = now()->startOfDay();

        $queues = Queue::with('visit.patient')
            ->where('specialization_id', $specialization->id)
            ->whereDate('created_at', $today)
            ->orderBy('queue_number')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'queue_number' => $q->queue_number,
                    'patient_name' => $q->visit->patient->name ?? 'N/A',
                    'status' => $q->status,
                    'source' => $q->source,
                    'checked_in' => $q->checked_in_at !== null,
                ];
            });

        return $queues->toArray();
    }
}
