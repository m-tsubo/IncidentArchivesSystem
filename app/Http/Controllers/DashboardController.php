<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Department;

use Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $incidents = Incident::all();
        $departments = Department::all();
        return view('dashboard', compact('incidents', 'departments'));
    }

    public function getIncidentsByYearMonth($year, $month)
    {
        // Eager Loadingを使用して関連するユーザーや部署の情報も取得
        $incidents = Incident::with(['user', 'user.department', 'department'])
                            ->whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->get();
        
        // レスポンスとして必要な形式にデータを整形
        $transformedIncidents = $incidents->map(function($incident) {
            return [
                'id' => $incident->id,
                'case_name' => $incident->case_name,
                'order_number' => $incident->order_number,
                'person_in_charge' => $incident->person_in_charge,
                'department_id' => $incident->department_id,
                'incident' => $incident->incident,
                'solution' => $incident->solution,
                'user_id' => $incident->user_id,
                'username' => optional($incident->user)->name,
                'departmentName' => optional($incident->user->department)->name,
                'detail_path' => $incident->detail_path,
                'deptName' => optional($incident->department)->name,
                'created_at' => $incident->created_at->format('Y-m-d'),
            ];
        });

        return response()->json($transformedIncidents);
    }

    public function getAllIncidents() {
        $incidents = Incident::with(['user', 'user.department', 'department'])->get();
    
        $transformedIncidents = $incidents->map(function($incident) {
            return [
                'id' => $incident->id,
                'case_name' => $incident->case_name,
                'order_number' => $incident->order_number,
                'person_in_charge' => $incident->person_in_charge,
                'department_id' => $incident->department_id,
                'incident' => $incident->incident,
                'solution' => $incident->solution,
                'user_id' => $incident->user_id,
                'username' => optional($incident->user)->name,
                'departmentName' => optional($incident->user->department)->name,
                'detail_path' => $incident->detail_path,
                'deptName' => optional($incident->department)->name,
                'created_at' => $incident->created_at->format('Y-m-d'),
            ];
        });
    
        return response()->json($transformedIncidents);
    }

    public function getBySearch(Request $request)
    {
        $section = $request->input('section');
        $keyword = $request->input('keyword');

        $query = Incident::with(['user', 'user.department', 'department']);

        if ($section && $keyword) {
            if ($section === 'username') {
                $query->whereHas('user', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%');
                });
            } elseif ($section === 'departmentName') {
                $query->whereHas('user.department', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%');
                });
            } else {
                $query->where($section, 'LIKE', '%' . $keyword . '%');
            }
        } elseif ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('case_name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('detail_path', 'LIKE', '%' . $keyword . '%')
                ->orWhere('order_number', 'LIKE', '%' . $keyword . '%')
                ->orWhere('person_in_charge', 'LIKE', '%' . $keyword . '%')
                ->orWhere('incident', 'LIKE', '%' . $keyword . '%')
                ->orWhere('solution', 'LIKE', '%' . $keyword . '%');
            })
            ->orWhereHas('user', function($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%');
            })
            ->orWhereHas('department', function($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%');
            });
        }

        $incidents = $query->get();
    
        // レスポンスとして必要な形式にデータを整形
        $transformedIncidents = $incidents->map(function($incident) {
            return [
                'id' => $incident->id,
                'case_name' => $incident->case_name,
                'order_number' => $incident->order_number,
                'person_in_charge' => $incident->person_in_charge,
                'department_id' => $incident->department_id,
                'incident' => $incident->incident,
                'solution' => $incident->solution,
                'user_id' => $incident->user_id,
                'username' => optional($incident->user)->name,
                'departmentName' => optional($incident->user->department)->name,
                'detail_path' => $incident->detail_path,
                'deptName' => optional($incident->department)->name,
                'created_at' => $incident->created_at->format('Y-m-d'),
            ];
        });
    
        return response()->json($transformedIncidents);
    }
    

}
