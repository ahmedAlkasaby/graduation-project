@php
    // ids stay unique when a page renders more than one single-image dropzone
    $uid = $input_id ?? \Illuminate\Support\Str::slug($name, '-');
    $fileId = 'input-' . $uid;
    $removedId = 'removed-' . $uid;
@endphp

<div class="form-group mb-4">
    <div class="dropzone needsclick dz-clickable @error($name) is-invalid @enderror"
        data-dropzone-name="{{ $name }}" data-input-id="{{ $fileId }}" data-removed-id="{{ $removedId }}"
        @if (!empty($existingImageUrl)) data-existing-image-url="{{ $existingImageUrl }}"  data-existing-image-size="{{ $image_size ?? null }}" @endif>
        <div class="dz-message needsclick">
            {{ __('Drag file here or click to upload') }}
        </div>
    </div>

    <input type="file" name="{{ $name }}" id="{{ $fileId }}" class="d-none" accept="image/*">

    <input type="hidden" name="{{ $name }}_removed" id="{{ $removedId }}" value="0">

    @error($name)
        <span class="text-danger d-block mt-2">
            {{ $message }}
        </span>
    @enderror
</div>
