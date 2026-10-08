<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use LogicException;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Authorize the given resource for the current user based on the request method.
     */
    protected function authorizeResource(string $class, ?string $parameter = null): ?\Illuminate\Database\Eloquent\Model
    {
        $param = $parameter ?? 'id';
        $method = strtoupper(request()->method());
        $uri = request()->path();

        // Determine ability from HTTP method + URI pattern
        if ($method === 'GET' && str_ends_with($uri, '/create')) {
            $ability = 'create';
        } elseif ($method === 'GET') {
            $id = $this->safeRouteParam($param);
            $ability = $id ? 'view' : 'viewAny';
        } elseif ($method === 'POST') {
            $ability = 'create';
        } elseif (in_array($method, ['PUT', 'PATCH'])) {
            $ability = 'update';
        } elseif ($method === 'DELETE') {
            $ability = 'delete';
        } else {
            $ability = 'viewAny';
        }

        $model = null;

        if (in_array($ability, ['view', 'update', 'delete'], true)) {
            $id = $this->safeRouteParam($param);
            if ($id) {
                $model = $class::findOrFail($id);
            }
        }

        if ($model) {
            $this->authorize($ability, $model);
        } else {
            $this->authorize($class, $ability);
        }

        return $model;
    }

    private function safeRouteParam(string $param): ?string
    {
        try {
            return request()->route($param);
        } catch (LogicException) {
            $uri = request()->path();
            $parts = explode('/', $uri);
            $last = end($parts);
            return is_numeric($last) ? $last : null;
        }
    }
}
