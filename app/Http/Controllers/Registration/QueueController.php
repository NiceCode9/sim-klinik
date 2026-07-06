<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\Visit;
use App\Models\VisitBill;
use App\Services\Registration\QueueService;

class QueueController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function index()
    {
        $specializations = Specialization::orderBy('name')->get();

        return view('registration.queue.index', compact('specializations'));
    }

    public function create()
    {
        $patients = Patient::orderBy('name')->get(['id', 'medical_record_number', 'name', 'nik']);
        $specializations = Specialization::orderBy('name')->get();

        return view('registration.queue.create', compact('patients', 'specializations'));
    }

    public function store()
    {
        $data = request()->validate([
            'patient_id' => 'required|exists:patients,id',
            'specialization_id' => 'required|exists:specializations,id',
        ]);

        $visit = Visit::create([
            'patient_id' => $data['patient_id'],
            'specialization_id' => $data['specialization_id'],
            'visit_date' => now()->toDateString(),
            'status' => 'registered',
            'registration_channel' => 'offline',
        ]);

        $queueNumber = $this->queueService->generateQueueNumber($data['specialization_id']);

        $visit->queue()->create([
            'queue_number' => $queueNumber,
            'specialization_id' => $data['specialization_id'],
            'status' => 'waiting',
            'source' => 'offline',
        ]);

        VisitBill::create(['visit_id' => $visit->id]);

        return redirect()->route('registration.queue.index')
            ->with('success', "Pasien terdaftar. Nomor antrian: {$queueNumber}");
    }

    public function checkIn(Visit $visit)
    {
        $queue = $visit->queue;

        $queue->update([
            'checked_in_at' => now(),
            'status' => 'waiting',
        ]);

        $visit->update(['status' => 'vital_check']);

        return redirect()->route('registration.queue.index')
            ->with('success', 'Pasien berhasil check-in.');
    }

    public function call(Visit $visit)
    {
        $queue = $visit->queue;

        $queue->update([
            'called_at' => now(),
            'status' => 'called',
        ]);

        $visit->update(['status' => 'waiting_doctor']);

        return redirect()->route('registration.queue.index')
            ->with('success', "Memanggil antrian nomor {$queue->queue_number}");
    }

    public function skip(Visit $visit)
    {
        $visit->queue->update(['status' => 'skipped']);
        $visit->update(['status' => 'cancelled']);

        return redirect()->route('registration.queue.index')
            ->with('success', 'Pasien dilewati.');
    }

    public function done(Visit $visit)
    {
        $visit->queue->update(['status' => 'done']);
        $visit->update(['status' => 'waiting_payment']);

        return redirect()->route('registration.queue.index')
            ->with('success', 'Pemeriksaan selesai.');
    }
}
