<?php
namespace Tests\Unit;

use App\Services\SubscriptionService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    public function test_map_debt_type()
    {
        $service = new SubscriptionService();
        $this->assertEquals('Préstamo', $service->mapDebtType('Loan'));
        $this->assertEquals('Otra Deuda', $service->mapDebtType('Other Debt'));
        $this->assertEquals('Tarjeta de Crédito', $service->mapDebtType('Credit Card'));
        $this->assertEquals('Desconocido', $service->mapDebtType('Unknown'));
    }

    public function test_transform_data()
    {
        $service = new SubscriptionService();

        $results = new Collection([
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
            ]
        ]);

        $transformedData = $service->transformData($results);
        $this->assertCount(1, $transformedData);
        $this->assertEquals('Préstamo', $transformedData[0]['debt_type']);
        $this->assertEquals('Diego Cedrón', $transformedData[0]['full_name']);
        $this->assertEquals('2026-01-15', $transformedData[0]['report_uploaded_at']);
    }
}