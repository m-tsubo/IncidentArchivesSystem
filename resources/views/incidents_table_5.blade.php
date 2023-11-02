
<style>
.main-title {
    display: flex;
    align-items: center;  /* タイトルとロゴを中央揃えにする */
}

.title {
    font-size: 32px;     /* 大きめのフォントサイズ */
    font-weight: bold;   /* 太字にする */
    
}

.logo {
    width: 50px;         /* ロゴの幅 */
    height: 50px;        /* ロゴの高さ */
}

</style>


<div class="main-title">
    <h1 class="title">デザイン課</h1>
    <img src="Drawing.png" alt="Logo" class="logo">
</div>

<!-- 年の選択 -->
<select id="yearSelector">
    <option value="2023">2023</option>
    <option value="2024">2024</option>
    <option value="2025">2025</option>
    <option value="2026">2026</option>
    <!-- <option value="2024">2024</option>
    <option value="2024">2024</option> -->
    <!-- 必要に応じて他の年を追加 -->
</select>

<!-- 月の選択 -->
<select id="monthSelector">
    <option value="01">1月</option>
    <option value="02">2月</option>
    <option value="03">3月</option>
    <option value="04">4月</option>
    <option value="05">5月</option>
    <option value="06">6月</option>
    <option value="07">7月</option>
    <option value="08">8月</option>
    <option value="09">9月</option>
    <option value="10">10月</option>
    <option value="11">11月</option>
    <option value="12">12月</option>
</select>

<!-- セクションの選択 -->
<select id="sectionSelector">
    <option value="">すべてのセクション</option>
    <option value="case_name">案件名</option>
    <option value="detail_path">詳細</option>
    <option value="order_number">注番</option>
    <option value="person_in_charge">担当者名</option>
    <option value="deptName">部署</option>
    <option value="incident">インシデント</option>
    <option value="solution">解決案</option>
    <option value="username">投稿者</option>
    <option value="departmentName">投稿者部署</option> 
</select>

<input type="text" id="searchInput" placeholder="キーワードを入力">
<button id="searchButton" class="btn btn-primary">検索</button>

<!-- インシデントのテーブル -->
<button id="deleteSelected" class="btn btn-danger mt-2">選択行を削除</button>
<table id="incident-table" class="table table-bordered table-striped mt-4">
    <!-- テーブルのヘッダーとボディ部分 -->
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
    // JavaScriptのコード
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
        // テーブル内検索
        $("#searchButton").on("click", function() {
            let value = $("#searchInput").val().toLowerCase();
            let keywords = value.split(' ');  // キーワードをスペースで分割

            $("#incident-table tbody tr").filter(function() {
                let rowText = $(this).text().toLowerCase();
                let isAllKeywordsPresent = keywords.every(function(keyword) {
                    return rowText.includes(keyword);
                });
                $(this).toggle(isAllKeywordsPresent);
            });
        });
        //年月のページングを追加
        $("#yearSelector, #monthSelector").change(function() {
            var selectedYear = $("#yearSelector").val();
            var selectedMonth = $("#monthSelector").val();
            loadIncidentsByYearMonth(selectedYear, selectedMonth);
        });

        function loadIncidentsByYearMonth(year, month) {
            // すべての行を削除
            $("#incident-table tbody").empty();

            $.get("/get-incidents-by-year-month/" + year + "/" + month, function(data) {
                data.forEach(function(incident) {
                    var row = createIncidentRow(incident);
                    $("#incident-table tbody").prepend(row);
                });
            });
        }

        var currentYear = new Date().getFullYear();
        var currentMonth = new Date().getMonth() + 1;
        if (currentMonth < 10) {
            currentMonth = '0' + currentMonth;
        }
        $("#yearSelector").val(currentYear);
        $("#monthSelector").val(currentMonth);
        loadIncidentsByYearMonth(currentYear, currentMonth);
    function loadIncidents() {
        $.get("/get-all-incidents", function(data) {
            // console.log(data);
            data.forEach(function(incident) {
                var row = createIncidentRow(incident);
                $("#incident-table tbody").prepend(row);
            });
        });
    }
    function createIncidentRow(data) {
        console.log("Received detail_path:", data.detail_path);
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
                <td>${data.username}：${data.departmentName}</td>
            </tr>
        `;
    }

   

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
                } else {
                    // エラーメッセージの表示などの処理をここに追加できます。
                }
            },
            error: function(data) {
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

    $("#searchButton").click(function() {
        var section = $("#sectionSelector").val();
        var keyword = $("#searchInput").val();

        loadIncidentsBySearch(section, keyword);
    });

    function loadIncidentsBySearch(section, keyword) {
        // すべての行を削除
        $("#incident-table tbody").empty();

        $.get("/dashboard/get-by-search", { section: section, keyword: keyword }, function(data) {
            data.forEach(function(incident) {
                var row = createIncidentRow(incident);
                $("#incident-table tbody").prepend(row);
            });
        });
    }


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
