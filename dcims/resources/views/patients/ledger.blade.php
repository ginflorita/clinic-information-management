<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            Ledger — <a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 55rem;">
            <div class="bg-white shadow-sm rounded p-4">
                @if ($entries->isEmpty())
                    <p class="text-secondary mb-0">No billing activity on file.</p>
                @else
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entries as $entry)
                                <tr>
                                    <td>{{ $entry['date'] }}</td>
                                    <td>{{ $entry['description'] }}</td>
                                    <td class="text-end">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '' }}</td>
                                    <td class="text-end">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '' }}</td>
                                    <td class="text-end">{{ number_format($entry['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-medium border-top">
                                <td colspan="4" class="text-end">Current Balance</td>
                                <td class="text-end {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($runningBalance, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
