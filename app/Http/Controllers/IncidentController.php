<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Department;

use Storage;


class IncidentController extends Controller
{
    public function create()
{
    // 部署情報を取得
    $departments = Department::all();
    $incidents = Incident::all(); // 全てのインシデントを取得

    
    return view('incident.create', compact('incidents','departments'));
}

public function store(Request $request) {
    $request->validate([
        'case_name' => 'required',
        'detail' => 'mimes:pdf,doc,docx,jpg,png,jpeg|max:2048',
        'order_number' => 'required',
        'person_in_charge' => 'required',
        'department_id' => 'required',
        'incident' => 'required',
        'solution' => 'required',
    ]);

    // ファイルアップロード処理
    $detailPath = null;
    if ($request->hasFile('detail')) {
        if ($request->file('detail')->isValid()) {
            $detailPath = $request->file('detail')->store('details', 'public');
        } else {
            return response()->json(['message' => 'Uploaded file is not valid.'], 400);
        }
    }

    $incident = Incident::create([
        'case_name' => $request->case_name,
        'detail_path' => $detailPath,
        'order_number' => $request->order_number,
        'person_in_charge' => $request->person_in_charge,
        'department_id' => $request->department_id,
        'incident' => $request->incident,
        'solution' => $request->solution,
        'user_id' => auth()->id(),
    ]);

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'incident' => [
                'id' => $incident->id,
                'case_name' => $incident->case_name,
                'order_number' => $incident->order_number,
                'person_in_charge' => $incident->person_in_charge,
                'department_id' => $incident->department_id,
                'incident' => $incident->incident,
                'solution' => $incident->solution,
                'user_id' => $incident->user_id,
                'username' => optional($incident->user)->name,  // 関連するユーザーの名前
                'departmentName' => optional($incident->user->department)->name,  // 関連するユーザーの部署の名前
                'detail_path' => $incident->detail_path,
            ],
            'message' => 'Incident added successfully.'
        ]);
    }
}
    // public function index()
    // {
    //     $incidents = Incident::all();
    //     return view('dashboard', compact('incidents'));
    // }

    
    public function getAllIncidents() {
        // Incidentに関連するuserとそのuserのdepartmentの情報をEager Loadingで取得
        $incidents = Incident::with(['user', 'user.department', 'department'])->get();
    
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
                'username' => optional($incident->user)->name,  // 関連するユーザーの名前
                'departmentName' => optional($incident->user->department)->name,  // 関連するユーザーの部署の名前
                'detail_path' => $incident->detail_path,
                'deptName' => optional($incident->department)->name,  // Incidentと直接関連する部署の名前
                'created_at' => $incident->created_at->format('Y-m-d'),
            ];
        });
    
        return response()->json($transformedIncidents);
    }
    
    

    public function destroy(Request $request)
{
    $ids = $request->input('ids');  // ここでインシデントのIDの配列を受け取ります
    \Log::info($ids);  // ここでログに出力します

    // インシデントのIDに基づいて複数のインシデントを削除します
    \App\Models\Incident::whereIn('id', $ids)->delete();

    return response()->json(['message' => 'Successfully deleted incidents']);
}

}
