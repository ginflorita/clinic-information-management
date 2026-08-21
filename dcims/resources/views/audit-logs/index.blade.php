<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Audit Log</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="GET" class="row row-cols-lg-auto g-2 align-items-end mb-3">
                    <div class="col-12">
                        <label class="form-label small text-secondary mb-1">Entity</label>
                        <select name="entity_type" class="form-select">
                            <option value="">All</option>
                            @foreach ($entityTypes as $type)
                                <option value="{{ $type }}" @selected($filters['entity_type'] === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-secondary mb-1">Action</label>
                        <select name="action" class="form-select">
                            <option value="">All</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected($filters['action'] === $action)>{{ ucfirst($action) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-secondary mb-1">From</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-secondary mb-1">To</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-secondary">Filter</button>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-link">Reset</a>
                    </div>
                </form>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Actor</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Record #</th>
                            <th>Changes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="text-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $log->actor?->name ?? 'System' }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $log->action === 'delete' ? 'danger' : ($log->action === 'create' ? 'success' : 'primary') }} text-capitalize">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $log->entity_type) }}</td>
                                <td>{{ $log->entity_id }}</td>
                                <td>
                                    @if ($log->old_values || $log->new_values)
                                        <details>
                                            <summary class="text-secondary small">View</summary>
                                            <div class="d-flex gap-3 mt-2">
                                                @if ($log->old_values)
                                                    <div>
                                                        <div class="small text-secondary">Before</div>
                                                        <pre class="small bg-light p-2 rounded mb-0">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                                @if ($log->new_values)
                                                    <div>
                                                        <div class="small text-secondary">After</div>
                                                        <pre class="small bg-light p-2 rounded mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </details>
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
                new DataTable('#data-table', { order: [[0, 'desc']] });
            });
        </script>
    @endpush
</x-app-layout>
