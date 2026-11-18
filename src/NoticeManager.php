<?php

namespace JeffersonGoncalves\UsPrivacyNotices;

use Illuminate\Support\Collection;
use JeffersonGoncalves\UsPrivacy\UsPrivacy;
use JeffersonGoncalves\UsPrivacyNotices\Models\ProcessingActivity;

/**
 * @phpstan-type Row array{model: class-string, table: string, column: string, category: string, purpose: string|null, sensitive_type: string|null, retention: string|null, anonymize: string, sold: bool, targeted_advertising: bool, profiling: bool}
 */
class NoticeManager
{
    public function __construct(protected UsPrivacy $privacy) {}

    /**
     * Everything the privacy notice has to disclose under the state laws: the categories of personal
     * data processed, the purposes, the categories shared with third parties and those third parties,
     * whether you sell personal data or use it for targeted advertising or profiling, the sensitive
     * data you process, and how consumers exercise their rights and appeal.
     *
     * @return array{business: array<string, mixed>, data_categories: list<string>, sensitive: list<string>, sells: bool, targeted_advertising: bool, profiling: bool, third_parties: list<string>, shared_categories: list<string>, activities: list<array<string, mixed>>}
     */
    public function export(): array
    {
        $inventory = $this->inventory();
        $byModel = $inventory->groupBy('model');
        $activities = ProcessingActivity::query()->orderBy('name')->get();
        $sharing = $activities->filter(fn (ProcessingActivity $a): bool => $a->thirdParties() !== []);

        return [
            'business' => (array) config('us-privacy-notices.business', []),
            'data_categories' => $this->unique($activities->flatMap(fn (ProcessingActivity $a): array => $a->data_categories ?? [])),
            'sensitive' => $this->sensitiveTypes($inventory),
            'sells' => $this->sells(),
            'targeted_advertising' => $this->targetsAdvertising(),
            'profiling' => $this->profiles(),
            'third_parties' => $this->unique($sharing->flatMap(fn (ProcessingActivity $a): array => $a->thirdParties())),
            'shared_categories' => $this->unique($sharing->flatMap(fn (ProcessingActivity $a): array => $a->data_categories ?? [])),
            'activities' => $activities->map(function (ProcessingActivity $activity) use ($byModel): array {
                $rows = collect($activity->models ?? [])->flatMap(fn (string $model) => $byModel->get($model, collect()));

                return [
                    'name' => $activity->name,
                    'purpose' => $activity->purpose,
                    'consumers' => $activity->consumers ?? [],
                    'data_categories' => $activity->data_categories ?? [],
                    'sensitive' => $this->sensitiveTypes($rows),
                    'personal_data' => $rows->map(fn (array $row): array => [
                        'table' => $row['table'],
                        'column' => $row['column'],
                        'sensitive_type' => $row['sensitive_type'],
                        'sensitive' => $row['category'] === 'sensitive',
                        'sold' => $row['sold'],
                        'targeted_advertising' => $row['targeted_advertising'],
                        'profiling' => $row['profiling'],
                        'retention' => $row['retention'],
                    ])->values()->all(),
                    'sources' => $activity->sources ?? [],
                    'recipients' => $activity->recipients ?? [],
                    'retention' => $activity->retention,
                ];
            })->values()->all(),
        ];
    }

    public function sells(): bool
    {
        return $this->inventory()->contains('sold', true);
    }

    public function targetsAdvertising(): bool
    {
        return $this->inventory()->contains('targeted_advertising', true);
    }

    public function profiles(): bool
    {
        return $this->inventory()->contains('profiling', true);
    }

    public function processesSensitive(): bool
    {
        return $this->inventory()->contains('category', 'sensitive');
    }

    /** The "Your Privacy Choices" (opt-out) link is needed when you sell, target ads or profile. */
    public function needsOptOutLink(): bool
    {
        return $this->sells() || $this->targetsAdvertising() || $this->profiles();
    }

    /**
     * @return Collection<int, Row>
     */
    protected function inventory(): Collection
    {
        return collect($this->privacy->inventory());
    }

    /**
     * @param  Collection<int, Row>  $rows
     * @return list<string>
     */
    protected function sensitiveTypes(Collection $rows): array
    {
        return $this->unique($rows->where('category', 'sensitive')->map(fn (array $row): string => (string) ($row['sensitive_type'] ?? 'other')));
    }

    /**
     * @param  Collection<array-key, mixed>  $values
     * @return list<string>
     */
    protected function unique(Collection $values): array
    {
        return $values->filter()->map(fn ($v): string => (string) $v)->unique()->sort()->values()->all();
    }
}
