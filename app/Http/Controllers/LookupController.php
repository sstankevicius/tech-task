<?php

namespace App\Http\Controllers;

use App\Services\GameLookupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class LookupController
 *
 * @package App\Http\Controllers
 */
class LookupController extends Controller
{
    private GameLookupServiceInterface $lookupService;

    /**
     * @param GameLookupServiceInterface $lookupService
     */
    public function __construct(GameLookupServiceInterface $lookupService)
    {
        $this->lookupService = $lookupService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function lookup(Request $request): JsonResponse
    {
        $type = trim(strtolower($request->get('type', '')));
        $username = trim($request->get('username', ''));
        $id = trim($request->get('id', ''));

        if (!$username && !$id) {
            return response()->json(['error' => 'Username or ID is required'], 400);
        }

        try {
            $result = $this->lookupService->lookup($type, $username ?: $id, (bool) $username);
            return response()->json($result);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
