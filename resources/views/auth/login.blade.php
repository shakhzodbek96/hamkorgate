@extends('layouts.master-without-nav')

@section('body')
    <body>
    @endsection

    @section('content')
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Unisoft Group</h5>
                                            <p>Добро пожаловать</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}" alt=""
                                             class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="auth-logo">
                                    <a href="index" class="auth-logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ URL::asset('/assets/images/logo-light.svg') }}" alt=""
                                                     class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>

                                    <a href="index" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ URL::asset('/assets/images/logo.svg') }}" alt=""
                                                     class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div>
                                    <x-alert-success/>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Электронная почта или номер
                                                телефона</label>
                                            <input name="email" type="text"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   @if(session()->has('otp_code')) style="pointer-events: none" @endif
                                                   value="{{ old('email', session()->get('email')) }}" id="email"
                                                   autocomplete="email" autofocus>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Пароль</label>
                                            <div
                                                class="input-group auth-pass-inputgroup @error('password') is-invalid @enderror">
                                                <input type="password" name="password"
                                                       class="form-control  @error('password') is-invalid @enderror"
                                                       id="userpassword" @if(session()->has('otp_code')) style="pointer-events: none" @endif
                                                       aria-label="Password" aria-describedby="password-addon" @if(session()->has('otp_code')) value="{{ old('password', session()->get('password')) }}" @endif>
                                                <button class="btn btn-light " type="button" id="password-addon"><i
                                                        class="mdi mdi-eye-outline"></i></button>
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        @if(session()->has('otp_code'))
                                            <div class="mb-3">
                                                <label for="otp_code" class="form-label">Код подтверждения <span id="otp_timer"></span></label>
                                                <input name="otp_code" type="text"
                                                       class="form-control @error('otp_code') is-invalid @enderror" id="otp_code"
                                                       autocomplete="otp_code" autofocus maxlength="4">
                                                @error('otp_code')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        @endif
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                Запомнить меня
                                            </label>
                                        </div>
                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">
                                                Войти
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">

                            <div>
                                @if(!\App\Models\User::exists())
                                <p>
                                    <a href="{{ url('register') }}" class="fw-medium text-primary">Зарегистрироваться </a> </p>
                                <p>@endif
                                    © IT Unisoft <script>
                                        document.write(new Date().getFullYear())
                                    </script>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <script src="{{ URL::asset('assets/libs/jquery/jquery.min.js')}}"></script>
        <script>
            $(document).ready(function (){
                // show password input value
                $("#password-addon").on('click', function () {
                    if ($(this).siblings('input').length > 0) {
                        $(this).siblings('input').attr('type') == "password" ? $(this).siblings('input').attr('type', 'input') : $(this).siblings('input').attr('type', 'password');
                    }
                })
            });
            const otpCodeExists = {{ Session::has('otp_code') ? 'true' : 'false' }};
            const otpExpiresAt = "{{ Session::get('otp_expires_at') }}";

            if (otpCodeExists && otpExpiresAt) {
                const timerElement = document.getElementById("otp_timer");
                const expiryTime = new Date(otpExpiresAt).getTime(); // Конвертируем в миллисекунды
                const currentTime = new Date().getTime(); // Текущее время в миллисекундах
                let remainingTime = Math.floor((expiryTime - currentTime) / 1000); // Оставшееся время в секундах

                function startOtpTimer() {
                    function updateTimer() {
                        if (remainingTime > 0) {
                            const minutes = Math.floor(remainingTime / 60);
                            const seconds = remainingTime % 60;
                            timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                            remainingTime--;
                        } else {
                            timerElement.textContent = "OTP просрочен";
                            clearInterval(timerInterval);
                            // Дополнительные действия после истечения времени
                        }
                    }

                    updateTimer(); // Обновить таймер сразу, чтобы не было задержки
                    const timerInterval = setInterval(updateTimer, 1000);
                }

                startOtpTimer();
            } else {
                document.getElementById("otp_timer").textContent = "OTP не задан";
            }
        </script>
        <!-- end account-pages -->
    @endsection
