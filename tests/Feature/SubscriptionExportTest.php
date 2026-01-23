<?php

namespace Tests\Unit;

use App\Exports\SubscriptionExport;
use App\Services\SubscriptionService;
use Tests\TestCase;

class SubscriptionExportTest extends TestCase
{
    public function test_it_maps_row_correctly()
    {
        $service = new SubscriptionService();

        $rows = [
            (object) [
                'report_id' => 1,
                'full_name' => 'Diego Cedrón',
                'document' => '45678901',
                'email' => 'diego.cedron@gmail.com',
                'phone' => '+51999000001',
                'company' => 'BCP',
                'debt_type' => 'Loan',
                'situation' => 'NOR',
                'delay' => 0,
                'entity' => 'Banco',
                'total_amount' => 15000.00,
                'line_total' => 10000.00,
                'line_used' => 5000.00,
                'report_created_at' => '2026-01-15',
                'state' => 'OK',
            ],
        ];

        $export = new SubscriptionExport(new \ArrayIterator($rows), $service);

        $mapped = $export->map($rows[0]);

        // Posiciones según map()
        $this->assertEquals(1, $mapped[0]);                    // report_id
        $this->assertEquals('Diego Cedrón', $mapped[1]);       // full_name
        $this->assertEquals('Préstamo', $mapped[6]);           // debt_type mapeado por servicio
        $this->assertEquals('2026-01-15', $mapped[13]);        // report_created_at
        $this->assertEquals('OK', $mapped[14]);                // state
    }

    public function test_export_iterator_returns_iterator()
    {
        $service = new SubscriptionService();

        $rows = [
            (object) ['report_id' => 1],
            (object) ['report_id' => 2],
        ];

        $it = new \ArrayIterator($rows);

        $export = new SubscriptionExport($it, $service);

        $this->assertInstanceOf(\Iterator::class, $export->iterator());
        $this->assertCount(2, iterator_to_array($export->iterator(), false));
    }

    public function test_export_has_correct_headings()
    {
        $service = new SubscriptionService();

        $export = new SubscriptionExport(new \ArrayIterator([]), $service);

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
        ], $export->headings());
    }

    public function test_it_exports_multiple_records_correctly()
    {
        $service = new SubscriptionService();

        $rows = [
            (object) [
                'report_id' => 1,
                'full_name' => 'Diego Cedrón',
                'document' => '45678901',
                'email' => 'diego.cedron@gmail.com',
                'phone' => '+51999000001',
                'company' => 'BCP',
                'debt_type' => 'Loan',
                'situation' => 'NOR',
                'delay' => 0,
                'entity' => 'Banco',
                'total_amount' => 15000.00,
                'line_total' => 10000.00,
                'line_used' => 5000.00,
                'report_created_at' => '2026-01-15',
                'state' => 'OK',
            ],
            (object) [
                'report_id' => 2,
                'full_name' => 'Juan Pérez',
                'document' => '23456789',
                'email' => 'juan.perez@gmail.com',
                'phone' => '+51999000002',
                'company' => 'BBVA',
                'debt_type' => 'Credit Card',
                'situation' => 'DEF',
                'delay' => 15,
                'entity' => 'Banco',
                'total_amount' => 20000.00,
                'line_total' => 15000.00,
                'line_used' => 12000.00,
                'report_created_at' => '2026-01-15',
                'state' => 'OK',
            ],
        ];

        $export = new SubscriptionExport(new \ArrayIterator($rows), $service);

        $mappedRows = [];
        foreach ($export->iterator() as $row) {
            $mappedRows[] = $export->map($row);
        }

        $this->assertCount(2, $mappedRows);
        $this->assertEquals('Diego Cedrón', $mappedRows[0][1]);     // full_name
        $this->assertEquals('Juan Pérez', $mappedRows[1][1]);       // full_name
        $this->assertEquals('Tarjeta de Crédito', $mappedRows[1][6]); // debt_type mapeado
    }
}
