<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Specialization;
use App\Models\Visit;
use App\Models\VisitBill;
use App\Services\Registration\QueueService;
use Illuminate\Support\Str;

class OnlineRegistrationController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function create()
    {
        $specializations = Specialization::orderBy('name')->get();

        return view('public.register', compact('specializations'));
    }

    public function store()
    {
        $data = request()->validate([
            'nik' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'specialization_id' => 'required|exists:specializations,id',
        ]);

        $patient = Patient::where('nik', $data['nik'])->first();

        if (! $patient) {
            $data['medical_record_number'] = 'RM-'.date('Ymd').'-'.Str::upper(Str::random(6));
            $patient = Patient::create($data);
        }

        $visit = Visit::create([
            'patient_id' => $patient->id,
            'specialization_id' => $data['specialization_id'],
            'visit_date' => now()->toDateString(),
            'status' => 'registered',
            'registration_channel' => 'online',
        ]);

        $queueNumber = $this->queueService->generateQueueNumber($data['specialization_id'], 'online');

        $visit->queue()->create([
            'queue_number' => $queueNumber,
            'specialization_id' => $data['specialization_id'],
            'status' => 'waiting_online_confirmation',
            'source' => 'online',
        ]);

        VisitBill::create(['visit_id' => $visit->id]);

        return redirect()->route('register.online.success')
            ->with('success', "Pendaftaran berhasil! Nomor antrian Anda: {$queueNumber}. Silakan check-in saat tiba di klinik.");
    }

    public function success()
    {
        return view('public.register-success');
    }
}
