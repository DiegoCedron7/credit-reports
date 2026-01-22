<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubscriptionExport implements FromCollection, WithMapping, WithHeadings
{
    protected $subscriptions;

    public function __construct($subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    public function collection()
    {
        return collect($this->subscriptions);
    }

    public function map($subscription): array
    {
        return [
            $subscription['id'],
            $subscription['full_name'],
            $subscription['document'],
            $subscription['email'],
            $subscription['phone'],
            $subscription['company'],
            $subscription['debt_type'],
            $subscription['status'],
            $subscription['delay'],
            $subscription['entity'],
            $subscription['total_amount'],
            $subscription['line_total'],
            $subscription['line_used'],
            $subscription['report_uploaded_at'],
            $subscription['state'],
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

