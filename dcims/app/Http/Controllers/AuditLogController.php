<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('actor')->latest('created_at');

        if ($entityType = $request->string('entity_type')->trim()->value()) {
            $query->where('entity_type', $entityType);
        }

        if ($action = $request->string('action')->trim()->value()) {
            $query->where('action', $action);
        }

        if ($dateFrom = $request->date('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->date('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return view('audit-logs.index', [
            'logs' => $query->get(),
            'entityTypes' => AuditLog::query()->distinct()->orderBy('entity_type')->pluck('entity_type'),
            'actions' => AuditLog::query()->distinct()->orderBy('action')->pluck('action'),
            'filters' => array_merge(
                ['entity_type' => '', 'action' => '', 'date_from' => '', 'date_to' => ''],
                $request->only(['entity_type', 'action', 'date_from', 'date_to'])
            ),
        ]);
    }
}
