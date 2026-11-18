<div class="dataTables_paginate paging_simple_numbers">
    {{  $data->appends(request()->except('page'))->links() }}
</div>    
<div class="clearfix"></div>
@include('admin.layouts.page-head')
{{--  __("Count") }} {{ $data->total() --}}
</div>

