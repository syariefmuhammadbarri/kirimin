<?php

namespace Tests\Unit;

use App\Models\Payment;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    public function test_payment_method_is_normalized_from_mock_values(): void
    {
        $this->assertSame('transfer', Payment::normalizePaymentMethod('transfer (mocked)'));
        $this->assertSame('transfer', Payment::normalizePaymentMethod('bank_transfer'));
        $this->assertSame('e-wallet', Payment::normalizePaymentMethod('gopay'));
        $this->assertSame('cash', Payment::normalizePaymentMethod('alfamart'));
        $this->assertSame('midtrans', Payment::normalizePaymentMethod('midtrans'));
    }
}
