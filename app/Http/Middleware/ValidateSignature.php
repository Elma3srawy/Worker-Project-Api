<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    use ApiResponses;
    /**
     * The names of the query string parameters that should be ignored.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'fbclid',
        // 'utm_campaign',
        // 'utm_content',
        // 'utm_medium',
        // 'utm_source',
        // 'utm_term',
    ];



    public function handle($request, Closure $next, ...$args)
    {
        try {
            return parent::handle($request, $next, ...$args);
        } catch (\Exception $e) {

            if ($e instanceof \Illuminate\Contracts\Encryption\DecryptException &&
                str_contains($e->getMessage(), 'The payload is invalid')) {
                return $this->errorResponse('Expired hash.', 401);
            }
            // You can customize the error response based on your needs
            return $this->errorResponse('Invalid signature.',500);
        }
    }

}
