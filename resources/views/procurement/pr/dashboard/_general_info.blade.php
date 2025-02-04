<div class="card card-info mb-1">
    <div class="card-header p-1">
        <h3 class="card-title">General Info</h3>
    </div>
    <div class="card-body p-1">
        <table class="table table-sm table-bordered table-striped">
            <tr>
                <th>Description</th>
                @foreach ($projectCodes as $projectCode)
                    <td class="text-right"><small>{{ $projectCode }}</small></td>
                @endforeach
                <td class="text-right"><small>Total</small></td>
            </tr>
            <tr>
                <td><small>Total PR Count</small></td>
                @foreach ($projectCodes as $projectCode)
                    <td class="text-right"><small>{{ $prCountsByProject[$projectCode] ?? 0 }}</small></td>
                @endforeach
                <td class="text-right"><small>{{ $totalPRs }}</small></td>
            </tr>
            <tr>
                <td><small>Open PR Count</small></td>
                @foreach ($projectCodes as $projectCode)
                    <td class="text-right"><small>{{ $openPrCountsByProject[$projectCode] ?? 0 }}</small></td>
                @endforeach
                <td class="text-right"><small>{{ $totalOpenPRs }}</small></td>
            </tr>
        </table>
    </div>
</div>
