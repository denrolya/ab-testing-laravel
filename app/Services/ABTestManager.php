<?php

namespace App\Services;

use App\Models\ABTest;
use App\Models\ABTestVariant;
use Illuminate\Database\Eloquent\Collection;

class ABTestManager
{
    public const SESSION_KEY = 'ab_test';

    public function initializeABTestsInSession(): void
    {
        $runningTests = $this->getRunningTests();
        if ($runningTests->isEmpty()) {
            return;
        }

        foreach ($runningTests as $abTest) {
            $this->handleABTestVariantSelection($abTest);
        }
    }

    /**
     * For the sake of simplicity for now I skip testing the arguments.
     * At this point checking additional conditions for the test and variant are not necessary
     * as the test and variant are already validated during distribution.
     *
     * @param string $testName
     * @param string $variantName
     * @return bool
     */
    public function isVariantSelected(string $testName, string $variantName): bool
    {
        return session(self::SESSION_KEY.'.'.$testName, '') === $variantName;
    }

    public function startTest(ABTest $test): void
    {
        if (!$test->isRunning()) {
            $test->start();
        }

        $this->handleABTestVariantSelection($test);
    }

    public function stopTest(ABTest $test): void
    {
        if ($test->isRunning()) {
            $test->stop();
        }

        session()->forget(self::SESSION_KEY.'.'.$test->name);
    }

    public function getRunningTests(): Collection
    {
        return ABTest::where('status', ABTest::STATUS_RUNNING)->with('variants')->get();
    }

    private function handleABTestVariantSelection(ABTest $test): void
    {
        $sessionKey = self::SESSION_KEY.'.'.$test->name;

        // If a variant has already been assigned for this test, skip it
        if (session()->has($sessionKey)) {
            return;
        }

        if ($selectedVariant = $this->selectABTestVariantUsingDistribution($test)) {
            session([$sessionKey => $selectedVariant->name]);
            cache()->increment($selectedVariant->slug);
        }
    }

    private function selectABTestVariantUsingDistribution(ABTest $test): ?ABTestVariant
    {
        $selectedVariant = $test->variants->first();
        $assignedVariants = $this->getABTestVariantsFromCache($test);
        $testVariantsAssignedTotalCount = array_sum($assignedVariants);

        if ($testVariantsAssignedTotalCount === 0) {
            return $selectedVariant;
        }

        $testVariantsSorted = $test->variants->sortByDesc('targeting_ratio');
        $totalVariantsRatio = $testVariantsSorted->sum('targeting_ratio');

        foreach ($testVariantsSorted as $currentVariant) {
            $currentVariantRatio = $expectedVariantRatio = 1;
            $currentVariantAssignedCount = $assignedVariants[$currentVariant->slug] ?? 0;

            if ($testVariantsAssignedTotalCount > 0 && $totalVariantsRatio > 0) {
                $currentVariantRatio = $currentVariantAssignedCount / $testVariantsAssignedTotalCount;
                $expectedVariantRatio = $currentVariant->targeting_ratio / $totalVariantsRatio;
            }

            if ($currentVariantRatio <= $expectedVariantRatio) {
                $selectedVariant = $currentVariant;
                break;
            }
        }

        return $selectedVariant;
    }

    private function getABTestVariantsFromCache(ABTest $test): array
    {
        $variantSlugs = $test->variants->pluck('slug')->toArray();

        return cache()->many($variantSlugs);
    }
}
