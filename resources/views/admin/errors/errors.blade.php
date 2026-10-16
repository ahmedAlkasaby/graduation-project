@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissible alert-disable" role="alert">
    @foreach ($errors->all() as $error) {{ $error }}<br> @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($message_success = Session::get('success'))
  <div class="alert alert-success alert-dismissible alert-disable" role="alert">
    {{ $message_success }}   
     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($message_info = Session::get('info'))

 <div class="alert alert-primary alert-dismissible alert-disable" role="alert">
    {{ $message_info }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($message_warning = Session::get('warning'))
  <div class="alert alert-warning alert-dismissible alert-disable" role="alert">
    {{ $message_warning }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($message_error = Session::get('error'))
  <div class="alert alert-danger alert-dismissible alert-disable" role="alert">
    {{ $message_error }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

