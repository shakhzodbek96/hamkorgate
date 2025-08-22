@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8 col-lg-8 offset-lg-2 offset-md-2 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <x-alert-success/>
                    <h4 class="card-title mb-4">Новый пользователь</h4>

                    <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="mb-3">
                                    <label for="useremail" class="form-label">Электронная почта</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="useremail"
                                           value="{{ old('email') }}" name="email" placeholder="Email" autofocus
                                           required>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Имя</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" id="username" name="name" autofocus required
                                           placeholder="ФИО">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="userpassword" class="form-label">Пароль</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="userpassword" name="password"
                                           autofocus required>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Телефон</label>
                                    <input type="text" class="form-control input-mask @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" id="phone" name="phone" autofocus required
                                           data-inputmask="'mask': '99-999-99-99'" im-insert="true">
                                    @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                     </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Выбрать роль</label>
                                    <select class="select2 form-control select2-multiple" multiple="multiple" name="roles[]"
                                            data-placeholder="Выбрать ...">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(\App\Services\Helpers\Check::isAdmin())
                                <div class="mb-3">
                                    <label class="form-label">Партнер</label>
                                    <select class="select2 form-control" name="partner_id"
                                            data-placeholder="Выбрать ...">
                                        <option value=""></option>
                                        @foreach($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="is_admin">Является администратором</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row mt-4">
                            <div class="text-lg-end text-sm-center">
                                <div>
                                    <button type="submit" class="btn btn-success w-md waves-effect waves-light"><i
                                            class="fas fa-save"></i> Создать пользователя
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary w-md">Отмена</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/form-mask.init.js') }}"></script>
@endsection
