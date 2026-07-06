<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Services\Registration\QueueService;

class DisplayAntrianController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function index()
    {
        $specializations = Specialization::orderBy('name')->get();

        return view('public.display-antrian', compact('specializations'));
    }

    public function data(Specialization $specialization)
    {
        $queue = $this->queueService->getQueueForSpecialization($specialization);

        return response()->json($queue);
    }

    public function current()
    {
        $specializations = Specialization::orderBy('name')->get();
        $data = [];

        foreach ($specializations as $spec) {
            $called = $spec->queues()
                ->where('status', 'called')
                ->whereDate('created_at', now()->startOfDay())
                ->with('visit.patient')
                ->orderBy('called_at', 'desc')
                ->first();

            $waitingCount = $spec->queues()
                ->whereIn('status', ['waiting', 'waiting_online_confirmation'])
                ->whereDate('created_at', now()->startOfDay())
                ->count();

            $data[] = [
                'specialization' => $spec->name,
                'current_number' => $called ? $called->queue_number : null,
                'patient_name' => $called ? ($called->visit->patient->name ?? '') : '',
                'waiting_count' => $waitingCount,
            ];
        }

        return response()->json($data);
    }
}
