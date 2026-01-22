<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Exports\SubscriptionExport;
use Maatwebsite\Excel\Facades\Excel;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $data = [];

        $this->subscriptionService->getSubscriptionDataForExport($validated['from'], $validated['to'], function ($chunkData) use (&$data) {
            $data = [...$data, ...$chunkData];
        });

        return Excel::download(new SubscriptionExport($data), 'subscription_report.xlsx');
    }
}
