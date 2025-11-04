<div class="card card-info mb-1">
    <div class="card-header p-1">
        <h3 class="card-title">General Info by Project</h3>
    </div>
    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        @foreach ($projectCodes ?? [] as $projectCode)
                            <td class="text-right"><small><strong>{{ $projectCode }}</strong></small></td>
                        @endforeach
                        <td class="text-right"><small><strong>Total</strong></small></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><small>Total PO Count</small></td>
                        @foreach ($projectCodes ?? [] as $projectCode)
                            <td class="text-right"><small>{{ number_format($poCountsByProject[$projectCode] ?? 0) }}</small></td>
                        @endforeach
                        <td class="text-right"><small><strong>{{ number_format($totalPOs ?? 0) }}</strong></small></td>
                    </tr>
                    <tr>
                        <td><small>Draft PO Count</small></td>
                        @foreach ($projectCodes ?? [] as $projectCode)
                            <td class="text-right"><small>{{ number_format($draftPoCountsByProject[$projectCode] ?? 0) }}</small></td>
                        @endforeach
                        <td class="text-right"><small><strong>{{ number_format($totalDraftPOs ?? 0) }}</strong></small></td>
                    </tr>
                    <tr>
                        <td><small>Submitted PO Count</small></td>
                        @foreach ($projectCodes ?? [] as $projectCode)
                            <td class="text-right"><small>{{ number_format($submittedPoCountsByProject[$projectCode] ?? 0) }}</small></td>
                        @endforeach
                        <td class="text-right"><small><strong>{{ number_format($totalSubmittedPOs ?? 0) }}</strong></small></td>
                    </tr>
                    <tr class="bg-light">
                        <td><small><strong>Total PO Value (in thousands)</strong></small></td>
                        @foreach ($projectCodes ?? [] as $projectCode)
                            <td class="text-right"><small><strong>Rp {{ number_format(($poValuesByProject[$projectCode] ?? 0) / 1000, 0, ',', '.') }}K</strong></small></td>
                        @endforeach
                        <td class="text-right"><small><strong>Rp {{ number_format(($totalPOValue ?? 0) / 1000, 0, ',', '.') }}K</strong></small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

