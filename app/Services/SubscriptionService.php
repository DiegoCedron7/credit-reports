<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function getSubscriptionDataForExport(string $from, string $to, \Closure $callback): void
    {
        $chunkSize = 100;

        $from .= " 00:00:00";
        $to .= " 23:59:59";


        DB::table('subscriptions as s')
            ->select(
                's.id',
                's.full_name',
                's.document as document',
                's.email as email',
                's.phone as phone',
                's.created_at as created_at',
                'sr.id as report_id',
                'sr.created_at as report_created_at',
                DB::raw("COALESCE(rl.bank, ro.entity, rc.bank) as company"),
                DB::raw("CASE 
                    WHEN rl.id IS NOT NULL THEN 'Loan' 
                    WHEN ro.id IS NOT NULL THEN 'Other Debt' 
                    WHEN rc.id IS NOT NULL THEN 'Credit Card' 
                    ELSE 'Unknown' 
                END as debt_type"),
                DB::raw("COALESCE(rl.status, '') as situation"),
                DB::raw("COALESCE(rl.expiration_days, ro.expiration_days, rc.line) as delay"),
                DB::raw("COALESCE(ro.entity, '') as entity"),
                DB::raw("COALESCE(rl.amount, ro.amount, NULL) as total_amount"),
                DB::raw("COALESCE(rc.line, NULL) as line_total"),
                DB::raw("COALESCE(rc.used, NULL) as line_used"),
                DB::raw("'OK' as state")
            )
            ->join('subscription_reports as sr', 's.id', '=', 'sr.subscription_id')
            ->leftJoin('report_loans as rl', 'sr.id', '=', 'rl.subscription_report_id')
            ->leftJoin('report_other_debts as ro', 'sr.id', '=', 'ro.subscription_report_id')
            ->leftJoin('report_credit_cards as rc', 'sr.id', '=', 'rc.subscription_report_id')
            ->whereBetween('s.created_at', [$from, $to])
            ->orderBy('s.id', 'asc')
            ->chunk($chunkSize, function ($results) use ($callback) {
                $chunkData = $this->transformData($results);
                $callback($chunkData);
            });

    }

    public function mapDebtType($debtType): string
    {
        return match ($debtType) {
            'Loan' => 'Préstamo',
            'Other Debt' => 'Otra Deuda',
            'Credit Card' => 'Tarjeta de Crédito',
            default => 'Desconocido',
        };
    }

    public function transformData($results): array
    {
        return $results->map(fn($result): array => [
            'id' => $result->report_id,
            'full_name' => $result->full_name,
            'document' => $result->document,
            'email' => $result->email,
            'phone' => $result->phone,
            'company' => $result->company,
            'debt_type' => $this->mapDebtType($result->debt_type),
            'status' => $result->situation,
            'delay' => $result->delay,
            'entity' => $result->entity,
            'total_amount' => $result->total_amount,
            'line_total' => $result->line_total,
            'line_used' => $result->line_used,
            'report_uploaded_at' => $result->report_created_at,
            'state' => $result->state,
        ])->toArray();
    }
}
