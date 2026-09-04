<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('audit-logs.index', [
            'logs' => $this->filtered($request)->get(),
            'entityTypes' => AuditLog::query()->distinct()->orderBy('entity_type')->pluck('entity_type'),
            'actions' => AuditLog::query()->distinct()->orderBy('action')->pluck('action'),
            'actors' => User::whereIn('id', AuditLog::query()->whereNotNull('actor_id')->distinct()->pluck('actor_id'))
                ->orderBy('name')
                ->get(),
            'filters' => $this->filters($request),
        ]);
    }

    public function export(Request $request): Response
    {
        $logs = $this->filtered($request)->get();

        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['When', 'Actor', 'Action', 'Entity', 'Record #', 'Before', 'After']);

        foreach ($logs as $log) {
            fputcsv($csv, [
                $log->created_at->toDateTimeString(),
                $log->actor?->name ?? 'System',
                $log->action,
                $log->entity_type,
                $log->entity_id,
                $log->old_values ? json_encode($log->old_values) : '',
                $log->new_values ? json_encode($log->new_values) : '',
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit-log-'.now()->format('Y-m-d-His').'.csv"',
        ]);
    }

    private function filtered(Request $request): Builder
    {
        $query = AuditLog::with('actor')->latest('created_at');
        $filters = $this->filters($request);

        if ($filters['entity_type']) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if ($filters['entity_id']) {
            $query->where('entity_id', $filters['entity_id']);
        }

        if ($filters['action']) {
            $query->where('action', $filters['action']);
        }

        if ($filters['actor_id']) {
            $query->where('actor_id', $filters['actor_id']);
        }

        if ($filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function filters(Request $request): array
    {
        return array_merge(
            ['entity_type' => '', 'entity_id' => '', 'action' => '', 'actor_id' => '', 'date_from' => '', 'date_to' => ''],
            $request->only(['entity_type', 'entity_id', 'action', 'actor_id', 'date_from', 'date_to'])
        );
    }
}
