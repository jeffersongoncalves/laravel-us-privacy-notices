<?php

namespace JeffersonGoncalves\UsPrivacyNotices\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One purpose for which you process personal data.
 *
 * @property string $name
 * @property string $purpose
 * @property list<string>|null $consumers e.g. customers, website visitors
 * @property list<string>|null $data_categories categories of personal data, as the notice names them (contact data, purchase history...)
 * @property list<class-string>|null $models models whose #[PersonalData] columns this activity uses
 * @property list<string>|null $sources
 * @property list<array{name: string, role?: string}>|null $recipients role: processor, third_party or affiliate
 * @property string|null $retention
 */
class ProcessingActivity extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('us-privacy-notices.table', 'us_privacy_processing_activities');
    }

    protected function casts(): array
    {
        return [
            'consumers' => 'array',
            'data_categories' => 'array',
            'models' => 'array',
            'sources' => 'array',
            'recipients' => 'array',
        ];
    }

    /**
     * Names of the recipients that are third parties: the notice lists them and the categories shared.
     *
     * @return list<string>
     */
    public function thirdParties(): array
    {
        return array_values(array_map(
            fn (array $recipient): string => $recipient['name'],
            array_filter($this->recipients ?? [], fn (array $recipient): bool => ($recipient['role'] ?? null) === 'third_party'),
        ));
    }
}
