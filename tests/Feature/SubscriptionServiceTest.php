<?php
namespace Tests\Unit;

use App\Services\SubscriptionService;
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
}
