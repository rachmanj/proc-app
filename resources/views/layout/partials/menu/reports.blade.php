@can('akses_report')
<li class="nav-item dropdown">
    <a id="dropdownReports" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle">
        <i class="fas fa-chart-bar mr-1"></i>Reports
    </a>
    <ul aria-labelledby="dropdownReports" class="dropdown-menu border-0 shadow">
        <li><a href="{{ route('reports.index') }}" class="dropdown-item">
            <i class="fas fa-home mr-2"></i>Reports Dashboard
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li class="dropdown-header">PR Reports</li>
        <li><a href="{{ route('reports.pr.status') }}" class="dropdown-item">
            <i class="fas fa-chart-pie mr-2"></i>PR Status Report
        </a></li>
        <li><a href="{{ route('reports.pr.aging') }}" class="dropdown-item">
            <i class="fas fa-clock mr-2"></i>PR Aging Report
        </a></li>
        <li><a href="{{ route('reports.pr.by-department') }}" class="dropdown-item">
            <i class="fas fa-building mr-2"></i>PR by Department
        </a></li>
        <li><a href="{{ route('reports.pr.by-project') }}" class="dropdown-item">
            <i class="fas fa-project-diagram mr-2"></i>PR by Project
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li class="dropdown-header">PO Reports</li>
        <li><a href="{{ route('reports.po.status') }}" class="dropdown-item">
            <i class="fas fa-chart-pie mr-2"></i>PO Status Report
        </a></li>
        <li><a href="{{ route('reports.po.value') }}" class="dropdown-item">
            <i class="fas fa-money-bill-wave mr-2"></i>PO Value Report
        </a></li>
        <li><a href="{{ route('reports.po.by-supplier') }}" class="dropdown-item">
            <i class="fas fa-truck mr-2"></i>PO by Supplier
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li class="dropdown-header">Approval Reports</li>
        <li><a href="{{ route('reports.approval.time-analysis') }}" class="dropdown-item">
            <i class="fas fa-stopwatch mr-2"></i>Approval Time Analysis
        </a></li>
        <li><a href="{{ route('reports.approval.by-approver') }}" class="dropdown-item">
            <i class="fas fa-user-check mr-2"></i>Approval by Approver
        </a></li>
        <li><a href="{{ route('reports.approval.bottleneck') }}" class="dropdown-item">
            <i class="fas fa-exclamation-triangle mr-2"></i>Bottleneck Analysis
        </a></li>
    </ul>
</li>
@endcan

