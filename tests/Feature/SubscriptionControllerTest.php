<?php

namespace Tests\Feature;

use App\Http\Controllers\SubscriptionController;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    public function test_it_validates_dates_for_export()
    {
        $data = [
            'from' => 'invalid-date',
            'to' => '2026-01-31',
        ];

        $controller = new SubscriptionController(new SubscriptionService());
        $request = Request::create('/reports/credit/export', 'GET', $data);

        try {
            $controller->export($request);

            $this->fail('Expected validation exception not thrown.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertTrue($e->validator->fails());
            $this->assertArrayHasKey('from', $e->validator->errors()->toArray());
            $this->assertEquals('The from field must be a valid date.', $e->validator->errors()->first('from'));
        }
    }

    public function test_it_validates_to_is_after_or_equal_from()
    {
        $data = [
            'from' => '2026-01-31',
            'to' => '2026-01-01',
        ];

        $controller = new SubscriptionController(new SubscriptionService());
        $request = Request::create('/reports/credit/export', 'GET', $data);

        try {
            $controller->export($request);

            $this->fail('Expected validation exception not thrown.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertTrue($e->validator->fails());
            $this->assertArrayHasKey('to', $e->validator->errors()->toArray());
            $this->assertEquals('The to field must be a date after or equal to from.', $e->validator->errors()->first('to'));
        }
    }
}
