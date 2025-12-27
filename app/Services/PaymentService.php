<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentService extends BaseService
{
    /**
     * Create payment record for a sale
     */
    public function createPaymentForSale(int $saleId, array $paymentData): int
    {
        return DB::transaction(function () use ($saleId, $paymentData) {
            $paymentId = DB::table('payments')->insertGetId([
                'payment_number' => 'PAY-' . str_pad(DB::table('payments')->max('id') + 1, 6, '0', STR_PAD_LEFT),
                'customer_id' => $paymentData['customer_id'] ?? null,
                'reservation_id' => $paymentData['reservation_id'] ?? null,
                'order_id' => $paymentData['order_id'] ?? null,
                'payment_amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'],
                'payment_status' => 'completed',
                'invoice_status' => 'pending',
                'payment_note' => $paymentData['notes'] ?? null,
                'transaction_id' => $paymentData['transaction_id'] ?? null,
                'payment_details' => $paymentData['payment_details'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create transaction record
            $this->createTransaction([
                'account_id' => $paymentData['account_id'] ?? 1,
                'reference_id' => $saleId,
                'reference_type' => 'sale',
                'type' => 'income',
                'amount' => $paymentData['amount'],
                'description' => 'Payment for sale #' . $saleId,
                'date' => now()->toDateString(),
            ]);

            // Update sale payment status
            DB::table('sales')->where('id', $saleId)->update([
                'payment_method' => $paymentData['payment_method'],
                'status' => 'completed',
                'updated_at' => now(),
            ]);

            Log::info('Payment created for sale', [
                'payment_id' => $paymentId,
                'sale_id' => $saleId,
                'amount' => $paymentData['amount'],
                'user_id' => Auth::id(),
            ]);

            return $paymentId;
        });
    }

    /**
     * Create transaction record
     */
    public function createTransaction(array $data): int
    {
        return DB::table('transactions')->insertGetId([
            'transaction_number' => 'TXN-' . str_pad(DB::table('transactions')->max('id') + 1, 6, '0', STR_PAD_LEFT),
            'account_id' => $data['account_id'],
            'reference_id' => $data['reference_id'] ?? null,
            'reference_type' => $data['reference_type'] ?? null,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'date' => $data['date'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Process installment payment
     */
    public function processInstallmentPayment(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = $data['total_amount'];
            $installmentAmount = $data['installment_amount'];
            $installments = $data['installments'];

            $results = [];

            for ($i = 0; $i < $installments; $i++) {
                $paymentId = DB::table('payments')->insertGetId([
                    'payment_number' => 'PAY-' . str_pad(DB::table('payments')->max('id') + 1, 6, '0', STR_PAD_LEFT),
                    'customer_id' => $data['customer_id'],
                    'payment_amount' => $installmentAmount,
                    'payment_method' => $data['payment_method'],
                    'payment_status' => 'completed',
                    'invoice_status' => 'pending',
                    'payment_note' => "Installment {$i+1} of {$installments}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create transaction for each installment
                $this->createTransaction([
                    'account_id' => $data['account_id'] ?? 1,
                    'reference_id' => $paymentId,
                    'reference_type' => 'payment',
                    'type' => 'income',
                    'amount' => $installmentAmount,
                    'description' => "Installment payment {$i+1}",
                    'date' => now()->toDateString(),
                ]);

                $results[] = $paymentId;
            }

            Log::info('Installment payment processed', [
                'customer_id' => $data['customer_id'],
                'total_amount' => $totalAmount,
                'installments' => $installments,
                'payment_ids' => $results,
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => true,
                'payment_ids' => $results,
                'message' => 'Installment payment processed successfully',
            ];
        });
    }

    /**
     * Get payment summary for a table
     */
    public function getTablePaymentSummary(int $tableId): array
    {
        $sales = DB::table('sales')
            ->where('table_id', $tableId)
            ->where('status', 'completed')
            ->get();

        $totalAmount = $sales->sum('total');
        $totalPaid = $sales->sum('paid');

        return [
            'total_sales' => $sales->count(),
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'remaining_balance' => $totalAmount - $totalPaid,
            'is_fully_paid' => $totalAmount <= $totalPaid,
        ];
    }

    /**
     * Process refund
     */
    public function processRefund(int $paymentId, float $refundAmount, string $reason = null): bool
    {
        return DB::transaction(function () use ($paymentId, $refundAmount, $reason) {
            $payment = DB::table('payments')->where('id', $paymentId)->first();

            if (!$payment) {
                throw new \Exception('Payment not found');
            }

            if ($refundAmount > $payment->payment_amount) {
                throw new \Exception('Refund amount cannot exceed payment amount');
            }

            // Update payment status to refunded
            DB::table('payments')->where('id', $paymentId)->update([
                'payment_status' => 'refunded',
                'payment_note' => ($payment->payment_note ?? '') . ' | Refunded: ' . $refundAmount,
                'updated_at' => now(),
            ]);

            // Create refund transaction
            $this->createTransaction([
                'account_id' => 1, // Default account
                'reference_id' => $paymentId,
                'reference_type' => 'refund',
                'type' => 'expense',
                'amount' => $refundAmount,
                'description' => 'Payment refund - ' . ($reason ?? 'No reason provided'),
                'date' => now()->toDateString(),
            ]);

            Log::info('Payment refunded', [
                'payment_id' => $paymentId,
                'refund_amount' => $refundAmount,
                'reason' => $reason,
                'user_id' => Auth::id(),
            ]);

            return true;
        });
    }

    /**
     * Get daily payment summary
     */
    public function getDailyPaymentSummary(string $date = null): array
    {
        $date = $date ?? now()->toDateString();

        $payments = DB::table('payments')
            ->whereDate('created_at', $date)
            ->where('payment_status', 'completed')
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('payment_amount'),
            'payment_methods' => [],
            'date' => $date,
        ];

        // Group by payment method
        $byMethod = $payments->groupBy('payment_method');
        foreach ($byMethod as $method => $methodPayments) {
            $summary['payment_methods'][$method] = [
                'count' => $methodPayments->count(),
                'amount' => $methodPayments->sum('payment_amount'),
            ];
        }

        return $summary;
    }
}
