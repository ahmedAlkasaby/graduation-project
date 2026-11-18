<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.holiday_requests.index') }}" 
               class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ __('All') }} 
                <span class="badge bg-white text-primary ms-1">{{ $statusCounts['all'] ?? 0 }}</span>
            </a>
            
            <a href="{{ route('admin.holiday_requests.index', ['status' => 'pending']) }}" 
               class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                {{ __('Pending') }} 
                <span class="badge bg-white text-warning ms-1">{{ $statusCounts['pending'] ?? 0 }}</span>
            </a>
            
            <a href="{{ route('admin.holiday_requests.index', ['status' => 'approved']) }}" 
               class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                {{ __('Approved') }} 
                <span class="badge bg-white text-success ms-1">{{ $statusCounts['approved'] ?? 0 }}</span>
            </a>
            
            <a href="{{ route('admin.holiday_requests.index', ['status' => 'rejected']) }}" 
               class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                {{ __('Rejected') }} 
                <span class="badge bg-white text-danger ms-1">{{ $statusCounts['rejected'] ?? 0 }}</span>
            </a>
            
            <a href="{{ route('admin.holiday_requests.index', ['status' => 'cancelled']) }}" 
               class="btn btn-sm {{ request('status') === 'cancelled' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                {{ __('Cancelled') }} 
                <span class="badge bg-white text-secondary ms-1">{{ $statusCounts['cancelled'] ?? 0 }}</span>
            </a>
        </div>
    </div>
</div>
