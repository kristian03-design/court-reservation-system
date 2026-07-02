<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentApiController extends Controller
{
    /**
     * Get participants for a tournament with search, filter, sorting, and pagination.
     */
    public function participants(Request $request, $id): JsonResponse
    {
        $tournament = Tournament::findOrFail($id);

        $query = $tournament->participants()->with('user');

        // 1. Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('team_name', 'like', "%{$search}%");
            });
        }

        // 2. Filter
        if ($request->has('filter') && !empty($request->filter)) {
            $filter = $request->filter;
            switch ($filter) {
                case 'teams':
                    $query->where(function ($q) {
                        $q->where('participant_type', 'team')
                          ->orWhereNotNull('team_name')
                          ->where('team_name', '!=', '');
                    });
                    break;
                case 'players':
                    $query->where(function ($q) {
                        $q->where('participant_type', 'individual')
                          ->orWhere(function ($sq) {
                              $sq->whereNull('team_name')
                                 ->orWhere('team_name', '');
                          });
                    });
                    break;
                case 'active':
                    $query->where('status', 'active')->where('is_eliminated', false);
                    break;
                case 'eliminated':
                    $query->where(function ($q) {
                        $q->where('is_eliminated', true)
                          ->orWhere('status', 'eliminated');
                    });
                    break;
                case 'seed':
                    $query->whereNotNull('seed')->where('seed', '>', 0);
                    break;
            }
        }

        // 3. Sort
        $sort = $request->query('sort', 'newest');
        switch ($sort) {
            case 'wins':
                $query->orderBy('wins', 'desc')->orderBy('seed', 'asc');
                break;
            case 'seed':
                $query->orderBy('seed', 'asc')->orderBy('display_name', 'asc');
                break;
            case 'name':
                $query->orderBy('display_name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 4. Pagination
        $perPage = $request->query('per_page', 12);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'participants' => $paginated->items(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage()
        ]);
    }

    /**
     * Get matches for a tournament to populate participant histories and schedules.
     */
    public function matchesData($id): JsonResponse
    {
        $tournament = Tournament::findOrFail($id);

        $matches = $tournament->matches()->with([
            'participant1',
            'participant2',
            'winner',
            'schedule.court',
            'sets'
        ])->get();

        return response()->json($matches);
    }
}
