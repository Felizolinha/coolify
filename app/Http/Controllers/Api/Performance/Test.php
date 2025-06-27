<?php

namespace App\Http\Controllers\Api\Performance;

use App\Jobs\Performance\TestJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Test
{
    public function dispatch(Request $request): JsonResponse
    {
        $request->validate([
            'jobs' => 'required|integer|min:1|max:10000',
        ]);

        $jobCount = $request->input('jobs');

        for ($i = 1; $i <= $jobCount; $i++) {
            TestJob::dispatch();
        }

        return response()->json([
            'message' => "Dispatched {$jobCount} performance test jobs",
            'dispatched_at' => now()->toISOString(),
        ]);
    }
}
