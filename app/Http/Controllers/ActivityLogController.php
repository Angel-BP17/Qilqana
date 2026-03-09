<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAccess($request);

        $query = ActivityLog::with('user')->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }
        if ($request->filled('model')) {
            $query->where('model', $request->input('model'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', '%' . $search . '%')
                    ->orWhere('model', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $models = ActivityLog::select('model')->distinct()->orderBy('model')->pluck('model');
        $users = User::orderBy('name')->get(['id', 'name', 'last_name']);

        return view('activity-logs.index', compact('logs', 'actions', 'models', 'users'));
    }

    protected function authorizeAccess(Request $request): void
    {
        $user = $request->user();
        $allowed = $user?->hasRole('ADMINISTRADOR') || $user?->can('modulo registro de actividades');
        if (!$allowed) {
            abort(403);
        }
    }
}
