@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'volunteer_id', 'city_id', 'region_id', 'limit'],
])

    <div class="col-sm-6">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Search by name, national ID or phone'),
                    'text_name' => 'search',
                    'text_value' => request()->input('search'),
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.select', [
                    'not_req' => true,
                    'label' => __('Volunteer'),
                    'select_name' => 'volunteer_id',
                    'select_value' => request()->input('volunteer_id'),
                    'select_function' => $volunteers,
                ])
            </div>
        </div>
    </div>

    {{-- The address filters: plain selects, so the region list can follow the city (address-js). --}}
    <div class="col-12">
        <div class="row" data-address-fields data-regions="{{ json_encode($regionsByCity) }}" data-choose="{{ __('All') }}">
            <div class="col-sm-6">
                <div class="box">
                    <div class="box-body">
                        <label class="form-label" for="filter-city">{{ __('City') }}</label>
                        <select name="city_id" id="filter-city" class="form-select" data-address-city>
                            @foreach ($cities as $cityId => $cityName)
                                <option value="{{ $cityId }}" @selected((string) request()->input('city_id') === (string) $cityId)>{{ $cityName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="box">
                    <div class="box-body">
                        <label class="form-label" for="filter-region">{{ __('Region') }}</label>
                        <select name="region_id" id="filter-region" class="form-select" data-address-region>
                            <option value="">{{ __('All') }}</option>
                            @foreach ($regionsByCity[request()->input('city_id')] ?? [] as $region)
                                <option value="{{ $region['id'] }}" @selected((string) request()->input('region_id') === (string) $region['id'])>{{ $region['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.charity_cases.components.address-js')

@include('admin.layouts.components.filter.footer')
