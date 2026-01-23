<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class SubscriptionService
{
    public function creditReportStream(string $from, string $to): LazyCollection
    {
        $from .= ' 00:00:00';
        $to .= ' 23:59:59';

        $loans = DB::table('subscriptions as s')
            ->join('subscription_reports as sr', 's.id', '=', 'sr.subscription_id')
            ->join('report_loans as rl', 'sr.id', '=', 'rl.subscription_report_id')
            ->whereBetween('sr.created_at', [$from, $to])
            ->selectRaw("
                CONCAT('L-', rl.id) as row_id,
                sr.id as report_id,
                s.full_name,
                s.document,
                s.email,
                s.phone,
                rl.bank as company,
                'Loan' as debt_type,
                rl.status as situation,
                rl.expiration_days as delay,
                '' as entity,
                rl.amount as total_amount,
                NULL as line_total,
                NULL as line_used,
                sr.created_at as report_created_at,
                'OK' as state
            ");

        $others = DB::table('subscriptions as s')
            ->join('subscription_reports as sr', 's.id', '=', 'sr.subscription_id')
            ->join('report_other_debts as ro', 'sr.id', '=', 'ro.subscription_report_id')
            ->whereBetween('sr.created_at', [$from, $to])
            ->selectRaw("
                CONCAT('O-', ro.id) as row_id,
                sr.id as report_id,
                s.full_name,
                s.document,
                s.email,
                s.phone,
                ro.entity as company,
                'Other Debt' as debt_type,
                '' as situation,
                ro.expiration_days as delay,
                ro.entity as entity,
                ro.amount as total_amount,
                NULL as line_total,
                NULL as line_used,
                sr.created_at as report_created_at,
                'OK' as state
            ");

        $cards = DB::table('subscriptions as s')
            ->join('subscription_reports as sr', 's.id', '=', 'sr.subscription_id')
            ->join('report_credit_cards as rc', 'sr.id', '=', 'rc.subscription_report_id')
            ->whereBetween('sr.created_at', [$from, $to])
            ->selectRaw("
                CONCAT('C-', rc.id) as row_id,
                sr.id as report_id,
                s.full_name,
                s.document,
                s.email,
                s.phone,
                rc.bank as company,
                'Credit Card' as debt_type,
                '' as situation,
                0 as delay,
                '' as entity,
                NULL as total_amount,
                rc.line as line_total,
                rc.used as line_used,
                sr.created_at as report_created_at,
                'OK' as state
            ");

        $union = $loans->unionAll($others)->unionAll($cards);

        $query = DB::query()
            ->fromSub($union, 't')
            ->orderBy('report_created_at')
            ->orderBy('row_id');

         return $query->cursor();
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
}
