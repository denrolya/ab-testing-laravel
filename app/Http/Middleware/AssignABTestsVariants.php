<?php

namespace App\Http\Middleware;

use App\Services\ABTestManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssignABTestsVariants
{
    public function __construct(
        protected ABTestManager $abTestService
    ) {
    }

    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->abTestService->initializeABTestsInSession();

        return $next($request);
    }
}
