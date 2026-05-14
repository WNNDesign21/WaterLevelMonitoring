<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Device;
use App\Models\WaterLevelHistory;
use Carbon\Carbon;

class WaterLevelHistoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'device_slug' => 'required|string|exists:devices,slug',
            'range' => 'required|string|in:daily,weekly,monthly,yearly,custom',
            'start_date' => 'required_if:range,custom|date',
            'end_date' => 'required_if:range,custom|date',
        ]);

        $device = Device::where('slug', $request->device_slug)->first();
        $range = $request->range;

        $query = WaterLevelHistory::where('device_id', $device->id);

        // Smart Aggregation Logic based on Range
        // Default: No grouping (hourly data)
        $select = [
            'recorded_at as t',
            'avg_tma as y',
            'min_tma as min',
            'max_tma as max'
        ];

        if ($range === 'daily') {
            $query->where('recorded_at', '>=', Carbon::now()->subDay());
        } elseif ($range === 'weekly') {
            $query->where('recorded_at', '>=', Carbon::now()->subWeek());
            // Group by 3 hours for weekly
            $query->selectRaw('
                DATE_FORMAT(recorded_at, "%Y-%m-%d %H:00:00") as t,
                AVG(avg_tma) as y,
                MIN(min_tma) as min,
                MAX(max_tma) as max
            ')->groupBy('t');
            $select = null; 
        } elseif ($range === 'monthly') {
            $query->where('recorded_at', '>=', Carbon::now()->subMonth());
            // Group by Day for monthly (approx 30 points)
            $query->selectRaw('
                DATE(recorded_at) as t,
                AVG(avg_tma) as y,
                MIN(min_tma) as min,
                MAX(max_tma) as max
            ')->groupBy('t');
            $select = null;
        } elseif ($range === 'yearly') {
            $query->where('recorded_at', '>=', Carbon::now()->subYear());
            // Group by Day for yearly (approx 365 points)
            $query->selectRaw('
                DATE(recorded_at) as t,
                AVG(avg_tma) as y,
                MIN(min_tma) as min,
                MAX(max_tma) as max
            ')->groupBy('t');
            $select = null;
        } elseif ($range === 'custom') {
            $query->whereBetween('recorded_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
            
            // Auto-downsample for custom range if > 30 days
            $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));
            if ($days > 30) {
                $query->selectRaw('DATE(recorded_at) as t, AVG(avg_tma) as y, MIN(min_tma) as min, MAX(max_tma) as max')->groupBy('t');
                $select = null;
            }
        }

        if ($select) {
            $query->select($select);
        }

        $history = $query->orderBy('t', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'device' => $device->name,
            'range' => $range,
            'data' => $history->map(function($item) {
                return [
                    't' => Carbon::parse($item->t)->toIso8601String(),
                    'y' => round($item->y, 2),
                    'min' => round($item->min, 2),
                    'max' => round($item->max, 2),
                ];
            })
        ]);
    }
}
