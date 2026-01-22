<?php

namespace Tests\Unit;

use App\Exports\SubscriptionExport;
use Tests\TestCase;

class SubscriptionExportTest extends TestCase
{
    public function test_it_exports_data_correctly()
    {
        $data = [
            [
                'id' => 1,
                'full_name' => 'Diego Cedrón',
                'document' => '45678901',
                'email' => 'diego.cedron@gmail.com',
                'phone' => '+51999000001',
                'company' => 'BCP',
                'debt_type' => 'Préstamo',
                'status' => 'NOR',
                'delay' => 0,
                'entity' => 'Banco',
                'total_amount' => 15000.00,
                'line_total' => 10000.00,
                'line_used' => 5000.00,
                'report_uploaded_at' => '2026-01-15',
                'state' => 'OK',
            ]
        ];

        $export = new SubscriptionExport($data);

        $mappedData = $export->collection()->toArray();

        $this->assertEquals('Diego Cedrón', $mappedData[0]['full_name']);
        $this->assertEquals('2026-01-15', $mappedData[0]['report_uploaded_at']);
    }

    public function test_export_has_correct_columns()
    {
        $data = [
            [
                'id' => 1,
                'full_name' => 'Diego Cedrón',
                'document' => '45678901',
                'email' => 'diego.cedron@gmail.com',
                'phone' => '+51999000001',
                'company' => 'BCP',
                'debt_type' => 'Préstamo',
                'status' => 'NOR',
                'delay' => 0,
                'entity' => 'Banco',
                'total_amount' => 15000.00,
                'line_total' => 10000.00,
                'line_used' => 5000.00,
                'report_uploaded_at' => '2026-01-15',
                'state' => 'OK',
            ]
        ];

        $export = new SubscriptionExport($data);

        $mappedData = $export->collection()->toArray();

        $this->assertArrayHasKey('id', $mappedData[0]);
        $this->assertArrayHasKey('full_name', $mappedData[0]);
        $this->assertArrayHasKey('document', $mappedData[0]);
        $this->assertArrayHasKey('email', $mappedData[0]);
        $this->assertArrayHasKey('phone', $mappedData[0]);
        $this->assertArrayHasKey('company', $mappedData[0]);
        $this->assertArrayHasKey('debt_type', $mappedData[0]);
        $this->assertArrayHasKey('status', $mappedData[0]);
        $this->assertArrayHasKey('delay', $mappedData[0]);
        $this->assertArrayHasKey('entity', $mappedData[0]);
        $this->assertArrayHasKey('total_amount', $mappedData[0]);
        $this->assertArrayHasKey('line_total', $mappedData[0]);
        $this->assertArrayHasKey('line_used', $mappedData[0]);
        $this->assertArrayHasKey('report_uploaded_at', $mappedData[0]);
        $this->assertArrayHasKey('state', $mappedData[0]);
    }

    public function test_export_has_correct_headings()
    {
        $export = new SubscriptionExport([]);

        $headings = $export->headings();

        $this->assertEquals([
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
        ], $headings);
    }


    public function test_it_exports_multiple_records_correctly()
    {
        $data = [
            [
                'id' => 1,
                'full_name' => 'Diego Cedrón',
                'document' => '45678901',
                'email' => 'diego.cedron@gmail.com',
                'phone' => '+51999000001',
                'company' => 'BCP',
                'debt_type' => 'Loan',
                'status' => 'NOR',
                'delay' => 0,
                'entity' => 'Banco',
                'total_amount' => 15000.00,
                'line_total' => 10000.00,
                'line_used' => 5000.00,
                'report_uploaded_at' => '2026-01-15',
                'state' => 'OK',
            ],
            [
                'id' => 2,
                'full_name' => 'Juan Pérez',
                'document' => '23456789',
                'email' => 'juan.perez@gmail.com',
                'phone' => '+51999000002',
                'company' => 'BBVA',
                'debt_type' => 'Credit Card',
                'status' => 'DEF',
                'delay' => 15,
                'entity' => 'Banco',
                'total_amount' => 20000.00,
                'line_total' => 15000.00,
                'line_used' => 12000.00,
                'report_uploaded_at' => '2026-01-15',
                'state' => 'OK',
            ]
        ];

        $export = new SubscriptionExport($data);
        $mappedData = $export->collection()->toArray();

        $this->assertCount(2, $mappedData);
        $this->assertEquals('Diego Cedrón', $mappedData[0]['full_name']);
        $this->assertEquals('Juan Pérez', $mappedData[1]['full_name']);
    }
}
