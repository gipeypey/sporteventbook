<?php

namespace App\Console\Commands;

use App\Models\PromoCode;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateBulkPromoCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promo:generate-bulk
                            {--prefix=RUNNER : Prefix for the promo codes}
                            {--count=100 : Number of codes to generate}
                            {--type=percentage : Discount type (percentage or fixed)}
                            {--value=10 : Discount value}
                            {--min-amount=0 : Minimum order amount}
                            {--usage-limit=1 : Usage limit per code}
                            {--days-valid=30 : Number of days the codes are valid}
                            {--name= : Name for all promo codes (optional)}
                            {--description= : Description for all promo codes (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate bulk promo codes with unique codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $prefix = strtoupper($this->option('prefix'));
        $count = (int) $this->option('count');
        $type = $this->option('type');
        $value = (float) $this->option('value');
        $minAmount = (float) $this->option('min-amount');
        $usageLimit = (int) $this->option('usage-limit');
        $daysValid = (int) $this->option('days-valid');
        
        $name = $this->option('name') ?? "Bulk Promo - {$prefix}";
        $description = $this->option('description') ?? "Generated bulk promo codes with prefix {$prefix}";

        $this->info("Generating {$count} promo codes with prefix: {$prefix}");
        $this->info("Discount: " . ($type === 'percentage' ? "{$value}%" : "Rp " . number_format($value, 0, ',', '.')));
        $this->info("Valid for: {$daysValid} days");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $created = 0;
        $skipped = 0;
        $now = now();
        $expiresAt = $now->addDays($daysValid);

        for ($i = 1; $i <= $count; $i++) {
            // Generate unique code with padding
            $codeNumber = str_pad($i, 4, '0', STR_PAD_LEFT);
            $code = "{$prefix}-{$codeNumber}";

            // Check if code already exists
            if (PromoCode::where('code', $code)->exists()) {
                $skipped++;
                $progressBar->advance();
                continue;
            }

            // Create promo code
            PromoCode::create([
                'code' => $code,
                'name' => $name,
                'description' => $description,
                'type' => $type,
                'value' => $value,
                'minimum_amount' => $minAmount > 0 ? $minAmount : null,
                'usage_limit' => $usageLimit > 0 ? $usageLimit : null,
                'used_count' => 0,
                'starts_at' => $now,
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]);

            $created++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Bulk Promo Code Generation Complete!");
        $this->newLine();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $created],
                ['Skipped (Already Exists)', $skipped],
                ['Total Processed', $count],
            ]
        );

        $this->newLine();
        $this->info("📋 Sample codes:");
        
        // Show first 5 codes as examples
        $sampleCodes = PromoCode::where('code', 'like', "{$prefix}-%")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->pluck('code');
        
        foreach ($sampleCodes as $code) {
            $this->line("   • {$code}");
        }

        if ($count > 5) {
            $this->line("   ... and " . ($created - 5) . " more");
        }

        $this->newLine();
        $this->info("💡 Tip: You can export all codes to CSV from the Admin Panel");

        return Command::SUCCESS;
    }
}
