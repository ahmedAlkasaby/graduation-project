{{--
    The sheet posts with the form and is parsed from its temporary path, so it
    never lands in public/. A plain file input rather than a Dropzone preview,
    since there is nothing to show a thumbnail of.
--}}
<div class="form-group mb-4">
    <input type="file" name="attachment" id="input-attachment"
        class="form-control @error('attachment') is-invalid @enderror"
        accept=".xlsx,.xls,.csv" required>

    <small class="text-muted d-block mt-2">
        {{ __('Maximum file size allowed is 10 MB') }}
    </small>

    @error('attachment')
        <span class="text-danger d-block mt-2">
            {{ $message }}
        </span>
    @enderror
</div>
