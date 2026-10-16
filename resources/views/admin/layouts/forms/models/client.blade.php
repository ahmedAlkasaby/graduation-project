<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Client') }}</label>
    @endif
    <select name="user_id" class="select2" required style="width: 100%">
        @if(isset($all_user))
        <option value="0" selected>{{ __("None") }}</option>
        @endif
        @if(!empty($users))
        @foreach($users as $client)
        <option value="{{ $client->id }}" @if(isset($user_id) && $user_id > 0 && $user_id == $client->id) selected @endif>
            @if($client->phone != NULL){{ $client->phone }} @endif - {{ $client->name }} </option>
        @endforeach
        @endif
    </select>
</div>
