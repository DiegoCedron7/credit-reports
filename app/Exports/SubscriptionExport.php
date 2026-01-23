<?php
namespace App\Exports;

use App\Services\SubscriptionService;
use Maatwebsite\Excel\Concerns\FromIterator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

readonly class SubscriptionExport implements FromIterator, WithMapping, WithHeadings
{
    public function __construct(
        private \Iterator           $rows,
        private SubscriptionService $subscriptionService
    ) {
    }

    public function iterator(): \Iterator
    {
        return $this->rows;
    }

    public function map($row): array
    {
        return [
            $row->report_id,
            $row->full_name,
            $row->document,
            $row->email,
            $row->phone,
            $row->company,
            $this->subscriptionService->mapDebtType($row->debt_type),
            $row->situation,
            $row->delay,
            $row->entity,
            $row->total_amount,
            $row->line_total,
            $row->line_used,
            $row->report_created_at,
            $row->state,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'DNI',
            'Email',
            'Teléfono',
            'Compañía',
            'Tipo de Deuda',
            'Situación',
            'Atraso',
            'Entidad',
            'Monto Total',
            'Línea Total',
            'Línea Usada',
            'Reporte Subido El',
            'Estado',
        ];
    }
}
