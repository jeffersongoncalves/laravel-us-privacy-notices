<?php

namespace JeffersonGoncalves\UsPrivacyNotices\Commands;

use Illuminate\Console\Command;
use JeffersonGoncalves\UsPrivacyNotices\NoticeManager;

class NoticesCommand extends Command
{
    protected $signature = 'us-privacy:notices {--json : Print everything the privacy notice discloses, as JSON}';

    protected $description = 'Show what your privacy notice discloses under the US state privacy laws';

    public function handle(NoticeManager $notices): int
    {
        $export = $notices->export();

        if ($this->option('json')) {
            $this->line((string) json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->table(
            ['Activity', 'Purpose', 'Categories', 'Recipients', 'Retention'],
            array_map(fn (array $activity): array => [
                $activity['name'],
                $activity['purpose'],
                implode(', ', $activity['data_categories']) ?: '-',
                implode(', ', array_column($activity['recipients'], 'name')) ?: '-',
                $activity['retention'] ?? '-',
            ], $export['activities']),
        );

        $this->line('Third parties: '.(implode(', ', $export['third_parties']) ?: 'none').'. Sensitive: '.(implode(', ', $export['sensitive']) ?: 'none').'.');

        if ($notices->needsOptOutLink()) {
            $this->warn('You sell personal data, target ads or profile: disclose it and post a "Your Privacy Choices" opt-out link.');
        }

        if ($notices->processesSensitive()) {
            $this->warn('You process sensitive data: most states require the consumer\'s consent first (laravel-us-privacy-opt-out).');
        }

        return self::SUCCESS;
    }
}
