<div class=" image-repeater">
    <div  data-repeater-list="all_image" >
        <div class="row">
        <div class="col-sm-12">
            @if(!isset($label_show))
            <label>{{ $image_title ?? __('Images') }}</label><br>
            @endif
        </div>
        </div>
        @if($image_count > 0)
        @foreach($product_galleries as $key => $v)
        <div  data-repeater-item>
            <div class="row">
            <div class="col-sm-12" >
                <div class="form-group">
                    <input name="id" type="hidden" value="{{ $v->id }}">
                    <input id="image_link_{{ $key }}" name="image_link" type="hidden" value="{{ $v->image }}">
                    <img src="{{ $v->image }}"  width="50%" height="auto" />
                    <a href="{{URL::asset('filemanager/dialog.php?type=1&akey=admin_panel&field_id=image_link_'.$key)}}" class="btn iframe-btn btn-success fa fa-download" type="button"> <span>{{ $upload ?? '' }}</span></a>
                    {{-- @include('admin.layouts.forms.buttons.delete-image') --}}
                    @include('admin.layouts.forms.buttons.delete')
                    <input  class="image_number" type="hidden" value="{{ ++$key }}">
                </div>
            </div>
            </div>
            <div class="clearfix"></div>
        </div>
        @endforeach
        @else
        <div  data-repeater-item>
            <div class="row">
            <div class="col-sm-12" >
                <div class="form-group">
                    <input name="id" type="hidden" value="0">
                    <input id="image_link_0" name="image_link" type="hidden" value="">
                    <img  src=""  width="50%" height="auto" style="display:none;" />
                    <a href="{{URL::asset('filemanager/dialog.php?type=1&akey=admin_panel&field_id=image_link_0')}}" class="btn iframe-btn btn-success fa fa-download" type="button"> <span>{{ $upload ?? "" }}</span></a>
                    @include('admin.layouts.forms.buttons.delete')
                    <input  class="image_number" type="hidden" value="1">
                </div>
            </div>
            </div>
            <div class="clearfix"></div>
        </div>
        @endif
    </div>
     @include('admin.layouts.forms.buttons.add')
</div>
