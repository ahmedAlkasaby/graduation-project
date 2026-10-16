@php
    // The login screen is always dark, whatever the site/user theme is. Set it here so the
    // <html> class, the stylesheets in head_login and the illustrations below all agree.
    $admin_theme_style = 'dark';
@endphp
<!DOCTYPE html>
<html lang="{{ $admin_language }}" data-theme="theme-default"
    dir="{{ $admin_dir }}"
    data-template="vertical-menu-template-no-customizer"
    class="{{ $admin_theme_style }}-style layout-navbar-fixed layout-menu-fixed layout-compact">

<head>
    @include('admin.layouts.meta', ['title' => __('Login')])
    @include('admin.layouts.head_login')
    @yield('after_head')
</head>

<body>
    <!-- Content -->
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 p-0">
                @php $illustration = $admin_theme_style === 'dark' ? 'dark' : 'light'; @endphp
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-login-illustration-' . $illustration . '.png') }}"
                        alt="auth-login-cover" class="img-fluid my-5 auth-illustration" />

                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $illustration . '.png') }}"
                        alt="auth-login-cover" class="platform-bg" />
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <!-- Logo -->
                    <div class="app-brand mb-4">
                        <a href="{{ route('home') }}" class="app-brand-link gap-2">
                            @include('admin.layouts.image-head', ['width' => 25])
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h3 class="mb-1">{{ __('Welcome to') }} {{ $company_name }} ! 👋</h3>
                    <p class=" mb-4">{{ __('Please sign-in to your account and start the adventure') }}</p>

                    <form id="formAuthentication" class="mb-3" action="{{ route('admin.login') }}" method="POST"
                        role="form">
                        {{ csrf_field() }}
                        <div class="mb-3">
                            <label class="form-label">{{ __('Phone or Email') }}</label>
                            <input type="text" class="form-control" name="email" value="{{ old('email') }}"
                                placeholder="{{ __('Phone or Email') }}" required autofocus />
                            @if ($errors->has('email'))
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    <div data-field="email" data-validator="emailAddress">{{ $errors->first('email') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">{{ __('Password') }}</label>

                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="{{ __('Password') }}" required aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                @if ($errors->has('password'))
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                        <div data-field="password" data-validator="notEmpty">
                                            {{ $errors->first('password') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" />
                                <label class="form-check-label" for="remember-me"> {{ __('Remember Me') }} </label>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100 btn-{{ $btn_class ?? 'info' }}"
                            type="submit">{{ __('Sign In') }}</button>
                    </form>


                    <!-- <div class="divider my-4">
                        <div class="divider-text">or</div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
                            <i class="tf-icons fa-brands fa-facebook-f fs-5"></i>
                        </a>

                        <a href="javascript:;" class="btn btn-icon btn-label-google-plus me-3">
                            <i class="tf-icons fa-brands fa-google fs-5"></i>
                        </a>

                        <a href="javascript:;" class="btn btn-icon btn-label-twitter">
                            <i class="tf-icons fa-brands fa-twitter fs-5"></i>
                        </a>
                    </div> -->
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>

    <!-- / Content -->
    @include('admin.layouts.foot')
    @include('admin.layouts.foot_login')

</body>

</html>
