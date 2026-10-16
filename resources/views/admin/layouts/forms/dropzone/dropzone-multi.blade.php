@php
    // several galleries can share a page (one per variant group), so the ids
    // must stay unique even when the field name repeats
    $uid = $input_id ?? \Illuminate\Support\Str::slug($name, '-');
    $fileId = 'input-multi-' . $uid;
    $existingId = 'existing-' . $uid;

    // the file input carries the new uploads, the hidden input the kept paths
    $fileName = $file_name ?? $name;
    $existingName = $existing_name ?? $name . '_existing';
@endphp

<div class="form-group mb-4">

    <div class="dropzone needsclick dz-clickable @error($name) is-invalid @enderror"
        data-dropzone-multi="{{ $name }}"
        data-input-id="{{ $fileId }}"
        data-existing-id="{{ $existingId }}"
        @if (!empty($existingBase)) data-existing-base="{{ $existingBase }}" @endif
        @if (!empty($existingImages)) data-existing-images="{{ $existingImages }}" @endif
        @if (!empty($existingSizes)) data-existing-sizes="{{ $existingSizes }}" @endif>
        <div class="dz-message needsclick">
            {{ __('Drag files here or click to upload') }}
        </div>
    </div>

    <input type="file" name="{{ $fileName }}[]" id="{{ $fileId }}" class="d-none" accept="image/*" multiple>

    <input type="hidden" name="{{ $existingName }}" id="{{ $existingId }}" value="{{ $existingImages ?? '' }}">

    @error($name)
        <span class="text-danger d-block mt-2">
            {{ $message }}
        </span>
    @enderror
</div>
