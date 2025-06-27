<?php

namespace App\Jobs\Performance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $jobId = uniqid('job_', true);

        $testDir = storage_path('data/coolify');
        if (! File::exists($testDir)) {
            File::makeDirectory($testDir, 0755, true);
        }

        $testData = $this->generateTestData();

        // I/O Intensive Operations
        $this->performFileOperations($testDir, $jobId, $testData);

        // CPU Intensive Operations
        $this->performCpuIntensiveOperations($testData);

        // Memory Intensive Operations
        $this->performMemoryIntensiveOperations($testData);

        // Mixed Operations
        $this->performMixedOperations($testDir, $jobId);

    }

    private function generateTestData(): array
    {
        return [
            'numbers' => range(1, 1000),
            'strings' => array_map(fn ($i) => "test_string_$i", range(1, 500)),
            'mixed' => array_merge(
                range(1, 300),
                array_map(fn ($i) => "mixed_$i", range(1, 200))
            ),
        ];
    }

    private function performFileOperations(string $testDir, string $jobId, array $testData): void
    {
        // Write multiple files with different content
        for ($i = 1; $i <= 5; $i++) {
            $filename = "{$testDir}/test_file_{$jobId}_{$i}.txt";
            $content = $this->generateFileContent($i * 100);
            File::put($filename, $content);
        }

        // Write JSON data
        $jsonFile = "{$testDir}/test_data_{$jobId}.json";
        File::put($jsonFile, json_encode([
            'job_id' => $jobId,
            'timestamp' => now()->toISOString(),
            'test_data' => $testData,
            'random_data' => array_map(fn () => rand(1, 1000), range(1, 100)),
        ]));

        // Write CSV data
        $csvFile = "{$testDir}/test_metrics_{$jobId}.csv";
        $csvContent = "id,value,timestamp\n";
        for ($i = 1; $i <= 50; $i++) {
            $csvContent .= "{$i},".rand(1, 1000).','.now()->addSeconds($i)->toISOString()."\n";
        }
        File::put($csvFile, $csvContent);

        // Read and process files (simulate log processing)
        $files = File::files($testDir);
        foreach (array_slice($files, -3) as $file) {
            $content = File::get($file);
            $lines = explode("\n", $content);
            // Simulate processing
            array_filter($lines, fn ($line) => strlen($line) > 10);
        }
    }

    private function performCpuIntensiveOperations(array $testData): void
    {
        // Mathematical calculations
        $results = [];
        for ($i = 1; $i <= 1000; $i++) {
            $results[] = sqrt($i) * sin($i) + cos($i * 2) + log($i + 1);
        }

        // Sorting operations
        $largeArray = array_merge($testData['numbers'], range(1001, 2000));
        shuffle($largeArray);
        sort($largeArray);
        rsort($largeArray);

        // String operations
        $textProcessing = '';
        foreach ($testData['strings'] as $str) {
            $textProcessing .= strtoupper($str).'_'.md5($str).'|';
        }

        // Prime number calculation
        $primes = [];
        for ($num = 2; $num <= 200; $num++) {
            $isPrime = true;
            for ($i = 2; $i <= sqrt($num); $i++) {
                if ($num % $i === 0) {
                    $isPrime = false;
                    break;
                }
            }
            if ($isPrime) {
                $primes[] = $num;
            }
        }
    }

    private function performMemoryIntensiveOperations(array $testData): void
    {
        // Create large arrays
        $largeArray1 = array_fill(0, 10000, range(1, 100));
        $largeArray2 = array_map(fn ($i) => array_fill(0, 50, "data_$i"), range(1, 200));

        // Array manipulations
        $flattened = array_merge(...$largeArray1);
        $unique = array_unique(array_merge($flattened, $testData['numbers']));
        $chunks = array_chunk($unique, 100);

        // String manipulations
        $longString = str_repeat('Performance test data ', 1000);
        $processed = [];
        for ($i = 0; $i < 50; $i++) {
            $processed[] = substr($longString, $i * 20, 100).md5($longString.$i);
        }

        // Simulate data structure operations
        $matrix = [];
        for ($i = 0; $i < 100; $i++) {
            $matrix[$i] = array_fill(0, 100, rand(1, 100));
        }

        // Matrix operations
        $transposed = [];
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < 100; $j++) {
                $transposed[$j][$i] = $matrix[$i][$j];
            }
        }
    }

    private function performMixedOperations(string $testDir, string $jobId): void
    {
        // Combine I/O, CPU, and memory operations
        $processedData = [];

        for ($batch = 1; $batch <= 3; $batch++) {
            // CPU: Generate data
            $batchData = array_map(fn ($i) => [
                'id' => $i,
                'hash' => md5("batch_{$batch}_item_{$i}"),
                'value' => pow($i, 2) + sqrt($i * $batch),
                'timestamp' => now()->addSeconds($i)->toISOString(),
            ], range(1, 100));

            // Memory: Store and manipulate
            $processedData["batch_$batch"] = $batchData;

            // I/O: Write batch results
            $batchFile = "{$testDir}/batch_{$jobId}_{$batch}.json";
            File::put($batchFile, json_encode($batchData));

            // CPU: Additional processing
            usort($batchData, fn ($a, $b) => $a['value'] <=> $b['value']);
        }

        // Final aggregated file
        $summaryFile = "{$testDir}/summary_{$jobId}.json";
        File::put($summaryFile, json_encode([
            'job_id' => $jobId,
            'total_batches' => 3,
            'completion_time' => now()->toISOString(),
            'data_summary' => array_map(fn ($batch) => count($batch), $processedData),
        ]));
    }

    private function generateFileContent(int $lines): string
    {
        $content = '';
        for ($i = 1; $i <= $lines; $i++) {
            $content .= "Line $i: ".str_repeat('Sample data ', rand(5, 15))."\n";
        }

        return $content;
    }
}
