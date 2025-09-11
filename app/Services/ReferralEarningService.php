<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserReferralEarning;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralEarningService
{
    // Commission rates (in percentage)
    const COMMISSION_RATES = [
        'subscription_upgrade' => 10, // 10% for subscription upgrades
        'first_payment' => 15,        // 15% for first payments
        'recurring_payment' => 5,     // 5% for recurring payments
        'bonus' => 100,               // 100% for bonuses (fixed amounts)
    ];

    // Minimum payout threshold
    const MIN_PAYOUT_THRESHOLD = 50.00; // $50 minimum before payout

    /**
     * Generate earnings for a referral when an affiliate makes a payment
     */
    public function generateEarning(
        User $affiliate,
        User $referrer,
        float $amount,
        string $type,
        string $description = null,
        int $referenceId = null,
        string $referenceType = null
    ): ?UserReferralEarning {
        try {
            DB::beginTransaction();

            // Calculate commission amount
            $commissionRate = self::COMMISSION_RATES[$type] ?? 0;
            $commissionAmount = ($amount * $commissionRate) / 100;

            // For bonus type, use the amount directly
            if ($type === UserReferralEarning::TYPE_BONUS) {
                $commissionAmount = $amount;
            }

            // Create the earning record
            $earning = UserReferralEarning::create([
                'user_id' => $referrer->id,
                'from_user_id' => $affiliate->id,
                'amount' => $commissionAmount,
                'description' => $description ?? "Commission from {$affiliate->name} - {$type}",
                'status' => UserReferralEarning::STATUS_PENDING,
                'type' => $type,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
            ]);

            DB::commit();

            Log::info("Referral earning generated", [
                'referrer_id' => $referrer->id,
                'affiliate_id' => $affiliate->id,
                'amount' => $commissionAmount,
                'type' => $type,
                'earning_id' => $earning->id,
            ]);

            return $earning;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate referral earning", [
                'referrer_id' => $referrer->id,
                'affiliate_id' => $affiliate->id,
                'amount' => $amount,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Generate earnings for subscription upgrade
     */
    public function generateSubscriptionUpgradeEarning(
        User $affiliate,
        float $subscriptionAmount,
        int $subscriptionId = null
    ): ?UserReferralEarning {
        if (!$affiliate->affiliated_user) {
            return null;
        }

        $referrer = User::find($affiliate->affiliated_user);
        if (!$referrer) {
            return null;
        }

        return $this->generateEarning(
            $affiliate,
            $referrer,
            $subscriptionAmount,
            UserReferralEarning::TYPE_SUBSCRIPTION_UPGRADE,
            "Commission from {$affiliate->name} subscription upgrade",
            $subscriptionId,
            'subscription'
        );
    }

    /**
     * Generate earnings for first payment
     */
    public function generateFirstPaymentEarning(
        User $affiliate,
        float $paymentAmount,
        int $paymentId = null
    ): ?UserReferralEarning {
        if (!$affiliate->affiliated_user) {
            return null;
        }

        $referrer = User::find($affiliate->affiliated_user);
        if (!$referrer) {
            return null;
        }

        return $this->generateEarning(
            $affiliate,
            $referrer,
            $paymentAmount,
            UserReferralEarning::TYPE_FIRST_PAYMENT,
            "Commission from {$affiliate->name} first payment",
            $paymentId,
            'payment'
        );
    }

    /**
     * Generate earnings for recurring payment
     */
    public function generateRecurringPaymentEarning(
        User $affiliate,
        float $paymentAmount,
        int $paymentId = null
    ): ?UserReferralEarning {
        if (!$affiliate->affiliated_user) {
            return null;
        }

        $referrer = User::find($affiliate->affiliated_user);
        if (!$referrer) {
            return null;
        }

        return $this->generateEarning(
            $affiliate,
            $referrer,
            $paymentAmount,
            UserReferralEarning::TYPE_RECURRING_PAYMENT,
            "Commission from {$affiliate->name} recurring payment",
            $paymentId,
            'payment'
        );
    }

    /**
     * Generate bonus earning
     */
    public function generateBonusEarning(
        User $referrer,
        float $bonusAmount,
        string $description = null
    ): ?UserReferralEarning {
        return UserReferralEarning::create([
            'user_id' => $referrer->id,
            'from_user_id' => $referrer->id, // Self-referenced for bonuses
            'amount' => $bonusAmount,
            'description' => $description ?? "Bonus payment",
            'status' => UserReferralEarning::STATUS_PENDING,
            'type' => UserReferralEarning::TYPE_BONUS,
        ]);
    }

    /**
     * Get total earnings for a user
     */
    public function getTotalEarnings(User $user, string $status = null): float
    {
        $query = UserReferralEarning::where('user_id', $user->id);
        
        if ($status) {
            $query->where('status', $status);
        }

        return $query->sum('amount');
    }

    /**
     * Get pending earnings for a user
     */
    public function getPendingEarnings(User $user): float
    {
        return $this->getTotalEarnings($user, UserReferralEarning::STATUS_PENDING);
    }

    /**
     * Get approved earnings for a user
     */
    public function getApprovedEarnings(User $user): float
    {
        return $this->getTotalEarnings($user, UserReferralEarning::STATUS_APPROVED);
    }

    /**
     * Get paid earnings for a user
     */
    public function getPaidEarnings(User $user): float
    {
        return $this->getTotalEarnings($user, UserReferralEarning::STATUS_PAID);
    }

    /**
     * Check if user has enough earnings for payout
     */
    public function canPayout(User $user): bool
    {
        $pendingAmount = $this->getPendingEarnings($user);
        return $pendingAmount >= self::MIN_PAYOUT_THRESHOLD;
    }

    /**
     * Get earnings summary for a user
     */
    public function getEarningsSummary(User $user): array
    {
        return [
            'total_earnings' => $this->getTotalEarnings($user),
            'pending_earnings' => $this->getPendingEarnings($user),
            'approved_earnings' => $this->getApprovedEarnings($user),
            'paid_earnings' => $this->getPaidEarnings($user),
            'can_payout' => $this->canPayout($user),
            'min_payout_threshold' => self::MIN_PAYOUT_THRESHOLD,
        ];
    }

    /**
     * Process bulk approval of earnings
     */
    public function approveEarnings(array $earningIds): int
    {
        return UserReferralEarning::whereIn('id', $earningIds)
            ->where('status', UserReferralEarning::STATUS_PENDING)
            ->update(['status' => UserReferralEarning::STATUS_APPROVED]);
    }

    /**
     * Process bulk payment of earnings
     */
    public function payEarnings(array $earningIds): int
    {
        return UserReferralEarning::whereIn('id', $earningIds)
            ->where('status', UserReferralEarning::STATUS_APPROVED)
            ->update([
                'status' => UserReferralEarning::STATUS_PAID,
                'paid_at' => now(),
            ]);
    }
} 