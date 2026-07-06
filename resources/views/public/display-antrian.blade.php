@extends('layouts.public')

@section('title', 'Display Antrian')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="text-center mb-4">
                <h1 class="display-6"><strong>SIMKLINIK</strong></h1>
                <p class="text-muted">Queue Display</p>
            </div>
        </div>
    </div>

    <div class="row" id="queue-display">
        @foreach ($specializations as $specialization)
            <div class="col-md-4 mb-4">
                <div class="card card-primary card-outline">
                    <div class="card-header text-center">
                        <div class="card-title h4">{{ $specialization->name }}</div>
                    </div>
                    <div class="card-body text-center">
                        <div class="current-number" data-spec-id="{{ $specialization->id }}">
                            <h1 class="display-1 fw-bold text-primary" id="number-{{ $specialization->id }}">-</h1>
                            <p class="text-muted" id="patient-{{ $specialization->id }}">&nbsp;</p>
                        </div>
                        <hr>
                        <p class="mb-0">Waiting: <strong id="waiting-{{ $specialization->id }}">0</strong></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-12 text-center text-muted">
            <small>Last updated: <span id="last-updated">-</span></small>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateQueue() {
            fetch('{{ route('display-antrian.current') }}')
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        document.getElementById('number-' + @json($specializations[0]->id ?? 1)).textContent = '...';
                    });

                    data.forEach(item => {
                        const specName = item.specialization.toLowerCase().replace(/\s+/g, '-');

                        document.querySelectorAll('.current-number').forEach(el => {
                            const card = el.closest('.card');
                            const title = card.querySelector('.card-title').textContent.trim();
                            if (title === item.specialization) {
                                const numEl = card.querySelector('.display-1');
                                const patientEl = numEl.parentElement.querySelector('p');
                                const waitingEl = card.querySelector('strong');

                                if (numEl) numEl.textContent = item.current_number || '-';
                                if (patientEl) patientEl.textContent = item.patient_name || '';
                                if (waitingEl) waitingEl.textContent = item.waiting_count || '0';
                            }
                        });
                    });

                    document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
                })
                .catch(err => console.error('Queue fetch error:', err));
        }

        updateQueue();
        setInterval(updateQueue, 5000);
    </script>
    @endpush
@endsection
