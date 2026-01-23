<?php
namespace App\Http\Controllers;

use App\Exports\SubscriptionExport;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

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

         $stream = $this->subscriptionService
             ->creditReportStream($validated['from'], $validated['to']);


        return Excel::download(
            new SubscriptionExport($stream->getIterator(), $this->subscriptionService),
            'subscription_report.xlsx'
        );
    }
}
