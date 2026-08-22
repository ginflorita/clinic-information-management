<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\QueueEntry;
use App\Models\TreatmentPlan;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(): View
    {
        $today = today();

        $todaysAppointments = Appointment::whereDate('scheduled_start', $today);

        $metrics = [
            'todays_appointments' => (clone $todaysAppointments)->count(),
            'todays_patients' => (clone $todaysAppointments)->distinct('patient_id')->count('patient_id'),
            'waiting_patients' => QueueEntry::whereDate('queue_date', $today)->where('status', 'waiting')->count(),
            'currently_treating' => QueueEntry::whereDate('queue_date', $today)->where('status', 'in_treatment')->count(),
            'completed_appointments' => (clone $todaysAppointments)->whereHas('encounter', fn ($q) => $q->where('status', 'completed'))->count(),
            'cancelled_appointments' => (clone $todaysAppointments)->where('status', 'cancelled')->count(),
            'no_show_appointments' => (clone $todaysAppointments)->where('status', 'no_show')->count(),
            'todays_revenue' => Payment::whereDate('payment_date', $today)->where('status', 'completed')->sum('amount'),
            'outstanding_balances' => Invoice::where('status', 'issued')->sum('balance'),
            'new_patients' => Patient::whereDate('created_at', $today)->count(),
            'pending_treatment_plans' => TreatmentPlan::whereIn('status', ['draft', 'presented'])->count(),
            'follow_up_patients' => (clone $todaysAppointments)->whereHas('appointmentType', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%follow%']))->distinct('patient_id')->count('patient_id'),
            'low_stock_items' => Product::where('is_active', true)->withSum('batches', 'quantity')->get()->filter->isLowStock()->count(),
            'expiring_inventory' => ProductBatch::expiringWithin(InventoryController::EXPIRY_WARNING_DAYS)->count(),
        ];

        return view('dashboard', [
            'metrics' => $metrics,
            'recentActivities' => $this->recentActivities(),
        ]);
    }

    private function recentActivities(): Collection
    {
        $activities = new Collection;

        foreach (Patient::latest()->take(5)->get() as $patient) {
            $activities->push([
                'datetime' => $patient->created_at,
                'description' => 'Patient registered: '.$patient->full_name,
            ]);
        }

        foreach (Encounter::with('patient')->latest('started_at')->take(5)->get() as $encounter) {
            $activities->push([
                'datetime' => $encounter->started_at,
                'description' => 'Encounter started: '.$encounter->patient->full_name,
            ]);
        }

        foreach (Payment::with('patient')->where('status', 'completed')->latest()->take(5)->get() as $payment) {
            $activities->push([
                'datetime' => $payment->created_at,
                'description' => 'Payment received: '.$payment->patient->full_name.' ('.number_format($payment->amount, 2).')',
            ]);
        }

        return $activities->sortByDesc('datetime')->take(10)->values();
    }
}
