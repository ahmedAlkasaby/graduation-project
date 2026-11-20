{{-- Turns #beneficiary-select into a select2 that searches profiles by name, national ID or phone. --}}
<script>
    $(function () {
        $('#beneficiary-select').select2({
            width: '100%',
            placeholder: @json(__('Search by name, national ID or phone.')),
            ajax: {
                url: @json(route('admin.charity_cases.lookup')),
                dataType: 'json',
                delay: 250,
                data: (params) => ({ search: params.term || '' }),
                processResults: (data) => data,
            },
        });
    });
</script>
