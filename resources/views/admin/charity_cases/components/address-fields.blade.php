{{--
    The optional address of a beneficiary. Everything here is optional, but
    once any part is filled the city is required (CharityCaseRequest). The
    region select follows the city. Plain selects, so it works inside the
    assistance form's quick-create dialog too.

    Needs $cities and $regionsByCity (CharityCaseService::addressOptions()).
--}}
<div class="row g-3" data-address-fields data-regions="{{ json_encode($regionsByCity) }}" data-choose="{{ __('Choose') }}">
    <div class="col-12">
        <h6 class="mb-0">{{ __('Address') }}</h6>
        <small class="text-muted">{{ __('Optional. Choose the city to save an address.') }}</small>
    </div>
    <div class="col-sm-6">
        <label class="form-label" for="address-city">{{ __('City') }}</label>
        <select name="address[city_id]" id="address-city" class="form-select" data-address-city>
            <option value="">{{ __('Choose') }}</option>
            @foreach ($cities as $cityId => $cityName)
                <option value="{{ $cityId }}" @selected((string) old('address.city_id', $charityCase->address->city_id ?? null) === (string) $cityId)>{{ $cityName }}</option>
            @endforeach
        </select>
        @error('address.city_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-sm-6">
        <label class="form-label" for="address-region">{{ __('Region') }}</label>
        <select name="address[region_id]" id="address-region" class="form-select" data-address-region>
            <option value="">{{ __('Choose') }}</option>
            @foreach ($regionsByCity[old('address.city_id', $charityCase->address->city_id ?? null)] ?? [] as $region)
                <option value="{{ $region['id'] }}" @selected((string) old('address.region_id', $charityCase->address->region_id ?? null) === (string) $region['id'])>{{ $region['name'] }}</option>
            @endforeach
        </select>
        @error('address.region_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="address-text">{{ __('Address') }}</label>
        <textarea name="address[address]" id="address-text" class="form-control" rows="2" maxlength="500">{{ old('address.address', $charityCase->address->address ?? null) }}</textarea>
        @error('address.address')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-sm-6">
        <label class="form-label" for="address-latitude">{{ __('Latitude') }}</label>
        <input type="number" name="address[latitude]" id="address-latitude" class="form-control" step="any" min="-90" max="90"
            value="{{ old('address.latitude', $charityCase->address->latitude ?? null) }}">
        @error('address.latitude')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-sm-6">
        <label class="form-label" for="address-longitude">{{ __('Longitude') }}</label>
        <input type="number" name="address[longitude]" id="address-longitude" class="form-control" step="any" min="-180" max="180"
            value="{{ old('address.longitude', $charityCase->address->longitude ?? null) }}">
        @error('address.longitude')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>
@include('admin.charity_cases.components.address-js')
