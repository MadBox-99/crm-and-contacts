<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Merges customers that share the same (team_id, email) pair so the
 * `customers_team_id_email_unique` index can be applied without violating
 * pre-existing duplicate rows.
 *
 * For each duplicate group the oldest non-trashed customer is kept and every
 * child row referencing a duplicate is re-pointed to that survivor before the
 * duplicates are hard-deleted.
 */
final class CustomerDeduplicator
{
    /**
     * Child tables carrying their own unique constraint that includes
     * `customer_id`. Collisions there are resolved by discarding the
     * duplicate's row before the reassignment.
     *
     * @var array<string, list<string>>
     */
    private const array UNIQUE_CHILD_KEYS = [
        'lead_scores' => ['team_id'],
        'campaign_customer' => ['campaign_id'],
    ];

    /**
     * @return int Number of duplicate customer records that were merged away.
     */
    public function deduplicate(): int
    {
        $groups = DB::table('customers')
            ->select('team_id', 'email')
            ->whereNotNull('team_id')
            ->whereNotNull('email')
            ->groupBy('team_id', 'email')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($groups->isEmpty()) {
            return 0;
        }

        $childTables = $this->tablesReferencingCustomers();
        $merged = 0;

        foreach ($groups as $group) {
            $merged += $this->mergeGroup((int) $group->team_id, (string) $group->email, $childTables);
        }

        return $merged;
    }

    /**
     * @param  list<string>  $childTables
     */
    private function mergeGroup(int $teamId, string $email, array $childTables): int
    {
        return DB::transaction(function () use ($teamId, $email, $childTables): int {
            $rows = DB::table('customers')
                ->where('team_id', $teamId)
                ->where('email', $email)
                ->orderByRaw('deleted_at is null desc')
                ->orderBy('id')
                ->get();

            if ($rows->count() < 2) {
                return 0;
            }

            $survivorId = (int) $rows->first()->id;
            $loserIds = $rows->skip(1)->pluck('id')->map(fn ($id): int => (int) $id)->all();

            foreach ($childTables as $table) {
                $this->reassignTable($table, $survivorId, $loserIds);
            }

            DB::table('customers')->whereIn('id', $loserIds)->delete();

            return count($loserIds);
        });
    }

    /**
     * @param  list<int>  $loserIds
     */
    private function reassignTable(string $table, int $survivorId, array $loserIds): void
    {
        if (array_key_exists($table, self::UNIQUE_CHILD_KEYS)) {
            $this->removeConflictingRows($table, self::UNIQUE_CHILD_KEYS[$table], $survivorId, $loserIds);
        }

        DB::table($table)
            ->whereIn('customer_id', $loserIds)
            ->update(['customer_id' => $survivorId]);
    }

    /**
     * Keep a single row per sibling-key across the merge group so the later
     * reassignment cannot violate a unique index. The survivor's own rows are
     * always preferred; any further row (from another duplicate) that repeats
     * an already-seen key is deleted.
     *
     * Keys are materialised in PHP rather than via a correlated subquery
     * because MySQL forbids referencing the delete target table in a subquery
     * (error 1093), and this also catches duplicate-vs-duplicate collisions.
     *
     * @param  list<string>  $siblingColumns
     * @param  list<int>  $loserIds
     */
    private function removeConflictingRows(string $table, array $siblingColumns, int $survivorId, array $loserIds): void
    {
        $rows = DB::table($table)
            ->whereIn('customer_id', array_merge([$survivorId], $loserIds))
            ->get(array_merge(['customer_id'], $siblingColumns))
            ->sortByDesc(fn (object $row): int => (int) $row->customer_id === $survivorId ? 1 : 0)
            ->values();

        $seen = [];

        foreach ($rows as $row) {
            $key = implode('|', array_map(static fn (string $column): string => (string) $row->{$column}, $siblingColumns));

            if (! isset($seen[$key])) {
                $seen[$key] = true;

                continue;
            }

            $query = DB::table($table)->where('customer_id', $row->customer_id);

            foreach ($siblingColumns as $column) {
                $query->where($column, $row->{$column});
            }

            $query->delete();
        }
    }

    /**
     * Driver-agnostic discovery of every table with a `customer_id` column.
     *
     * @return list<string>
     */
    private function tablesReferencingCustomers(): array
    {
        $tables = [];

        foreach (Schema::getTableListing(schemaQualified: false) as $table) {
            if ($table === 'customers') {
                continue;
            }

            if (in_array('customer_id', Schema::getColumnListing($table), true)) {
                $tables[] = $table;
            }
        }

        return $tables;
    }
}
