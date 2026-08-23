<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Referrals</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Referral #</th>
                            <th>Patient</th>
                            <th>Referring Provider</th>
                            <th>Receiving</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($referrals as $referral)
                            @php $transitions = $referral->availableTransitions(); @endphp
                            <tr>
                                <td>{{ $referral->referral_number }}</td>
                                <td><a href="{{ route('patients.show', $referral->patient) }}">{{ $referral->patient->full_name }}</a></td>
                                <td>{{ $referral->referringProvider->full_name }}</td>
                                <td>
                                    {{ $referral->receiving_name }}
                                    @if ($referral->receiving_specialty)
                                        <span class="text-secondary">({{ $referral->receiving_specialty }})</span>
                                    @endif
                                </td>
                                <td>{{ $referral->referral_date->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($referral->status) { 'draft' => 'secondary', 'sent' => 'primary', 'received' => 'info', 'completed' => 'success', 'cancelled' => 'danger', default => 'secondary' } }} text-capitalize">
                                        {{ $referral->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if (! empty($transitions))
                                        <form method="POST" action="{{ route('referrals.transition', $referral) }}" class="d-flex gap-1 justify-content-end">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                                @foreach ($transitions as $next)
                                                    <option value="{{ $next }}">{{ ucfirst($next) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new DataTable('#data-table', { order: [[4, 'desc']] });
            });
        </script>
    @endpush
</x-app-layout>
