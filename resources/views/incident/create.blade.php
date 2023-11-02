@extends('layouts.app')
<style>
.btn-primary {
    background-color: #007bff !important;
    border-color: #007bff !important;
}


</style>
@section('content')
<div class="container">
    <h1 class="mb-4">Create Incident</h1>

    @if ($errors->any())
        
    <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incident.store') }}" method="post" enctype="multipart/form-data">

        @csrf

        <div class="row">
            <div class="col-md-2 form-group">
                <label for="case_name">案件名</label>
                <input type="text" name="case_name" class="form-control" id="case_name" value="{{ old('case_name') }}">
                @error('case_name')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label for="detail">詳細 (ファイル)</label>
                <input type="file" name="detail" class="form-control" id="detail">
                @error('detail')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2 form-group">
                <label for="order_number">注番</label>
                <input type="text" name="order_number" class="form-control" id="order_number" value="{{ old('order_number') }}">
                @error('order_number')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2 form-group">
                <label for="person_in_charge">担当者名</label>
                <input type="text" name="person_in_charge" class="form-control" id="person_in_charge" value="{{ old('person_in_charge') }}">
                @error('person_in_charge')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2 form-group">
                <label for="department_id">部署</label>
                <select name="department_id" id="department_id" class="form-control">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 form-group">
                <label for="incident">インシデント</label>
                <textarea name="incident" id="incident" class="form-control" rows="3">{{ old('incident') }}</textarea>
                @error('incident')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 form-group">
                <label for="solution">解決案</label>
                <textarea name="solution" id="solution" class="form-control" rows="3">{{ old('solution') }}</textarea>
                @error('solution')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">追加</button>
        </div>

        
    </form>

    
    <button id="deleteSelected" class="btn btn-danger mt-2">選択行を削除</button>

    <h2>今月分の投稿</h2>
    
    <table id="incident-table" class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"> 投稿日</th>
                <th>案件名</th>
                <th>詳細</th>
                <th>注番</th>
                <th>担当者名</th>
                <th>部署</th>
                <th>インシデント</th>
                <th>解決案</th>
                <th>投稿者</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    

    <script>
        
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
    
        function loadIncidents() {
        $.get("/get-all-incidents", function(data) {
            // 現在の年と月を取得
            let currentDate = new Date();
            let currentYear = currentDate.getFullYear();
            let currentMonth = currentDate.getMonth();

            data.forEach(function(incident) {
                // 各インシデントの年と月を取得
                let incidentDate = new Date(incident.created_at);
                let incidentYear = incidentDate.getFullYear();
                let incidentMonth = incidentDate.getMonth();

                // 現在の年と月がインシデントの年と月と一致する場合のみ、インシデントを表示
                if(currentYear === incidentYear && currentMonth === incidentMonth) {
                    var row = createIncidentRow(incident);
                    $("#incident-table tbody").prepend(row);
                }
            });
        });
    }

    function createIncidentRow(data) {
        // console.log(data);
         var detailContent = data.detail_path ? `<a href="/storage/` + data.detail_path + `" target="_blank" class="btn btn-link btn-sm">プレビュー</a>` : 'ファイルなし';
        
        return `
            <tr data-incident-id="${data.id}">
                <td><input type="checkbox"> ${data.created_at}</td>
                <td>${data.case_name}</td>
                <td>${detailContent}</td>
                <td>${data.order_number}</td>
                <td>${data.person_in_charge}</td>
                <td>${data.deptName}</td>
                <td>
                    <div class="incident-content" style="max-height:50px; max-width:300px; overflow:auto">${data.incident}</div>
                    <button class="btn btn-link btn-sm view-full-inc">表示</button>
                </td>
                <td>
                    <div class="solution-content" style="max-height:50px; max-width:300px; overflow:auto">${data.solution}</div>
                    <button class="btn btn-link btn-sm view-full-sol">表示</button>
                </td>
                <td>${data.username}<br>${data.departmentName}</td>
            </tr>
        `;
    }

    loadIncidents();

    $("form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('incident.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data); // deptName をログに出力
                if (data.success) {
                    var row = createIncidentRow({
                        id: data.incident.id,
                        case_name: data.incident.case_name,
                        detail_path: data.incident.detail_path,
                        order_number: data.incident.order_number,
                        person_in_charge: data.incident.person_in_charge,
                        deptName: data.incident.deptName,
                        departmentName: $("#department_id option:selected").text(),
                        incident: data.incident.incident,
                        solution: data.incident.solution,
                        username: "{{ Auth::user()->name }}"
                    });
                    $("#incident-table tbody").prepend(row);
                    e.target.reset();

                    alert('インシデントが正常に追加されました。');

                    location.reload();
                } else {
                    // エラーメッセージの表示などの処理をここに追加できます。
                }
            },
            error: function(data) {
                // console.log('HTTP Status:', data.status);
                // console.log('Response:', data.responseText);

                if (data.status === 422) {
                    var errors = data.responseJSON.errors;
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '\n';
                    });
                    alert('エラーが発生しました。\n' + errorMessage);
                } else {
                    alert('エラーが発生しました。');
                }
            }
        });

    });


    $(document).on("click", ".view-full-inc, .view-full-sol", function() {
        var content = $(this).prev("div");
        if (content.css("max-height") == "50px") {
            content.css("max-height", "none");
            $(this).text("隠す");
        } else {
            content.css("max-height", "50px");
            $(this).text("表示");
        }
    });

    $("#selectAll").click(function() {
        var isChecked = $(this).prop("checked");
        $("#incident-table tbody tr td:first-child input:checkbox").prop("checked", isChecked);
    });

    $("#deleteSelected").click(function() {
        var selectedIds = [];
        $("#incident-table tbody tr").each(function() {
            if ($(this).find("td:first-child input:checkbox").prop("checked")) {
                var incidentId = $(this).data('incident-id');
                selectedIds.push(incidentId);
            }
        });

        if (selectedIds.length && confirm('選択されたインシデントを削除してもよろしいですか？')) {
            $.ajax({
                url: "/incident/mass-delete",
                type: "DELETE",
                data: {
                    ids: selectedIds
                },
                success: function(response) {
                    $("#incident-table tbody tr").each(function() {
                        if ($(this).find("td:first-child input:checkbox").prop("checked")) {
                            $(this).remove();
                        }
                    });
                    alert(response.message);
                },
                error: function() {
                    alert('エラーが発生しました。');
                }
            });
        }
    });
});

    </script>
</div>
@endsection
