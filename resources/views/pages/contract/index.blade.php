@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Контракты</h4>
                <div class="page-title-right btn-group-sm">
                    <a href="#" type="button"
                       data-bs-toggle="offcanvas" data-bs-target="#openActions" aria-controls="offcanvasRight"
                       class="btn btn-outline-dark btn-rounded waves-effect waves-light me-2">
                        <i class="bx bx-cog align-middle font-size-16"></i> Активности</a>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="openActions"
                         aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                        <div class="offcanvas-header">
                            <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Активности для контрактов</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <div class="btn-group-vertical gap-4">
                                @can('Синхронизация карт клиентов')
                                    <a href="{{ route('clients.syncCards',request()->all()) }}"
                                       onclick="return confirm('Confirm the action!');"
                                       class="btn btn-primary waves-effect waves-light me-1">
                                        <i class="fa fa-sync align-middle font-size-16"></i> Синхронизация карт клиентов
                                    </a>
                                @endcan
                                @can('Запуск платного поиска по UZCARD картам')
                                    <a href="{{ route('contracts.updateUzCards',request()->all()) }}"
                                       onclick="return confirm('Вы уверены, что хотите запустить платный поиск по UZCARD картам?');"
                                       class="btn btn-primary waves-effect waves-light me-1">
                                        <i class="fa fa-search-dollar align-middle font-size-16"></i> Запуск платного поиска по UZCARD картам
                                    </a>
                                @endcan
                                @can('Запуск платного поиска по HUMO картам')
                                    <a href="{{ route('contracts.updateHumoCards',request()->all()) }}"
                                       onclick="return confirm('Вы уверены, что хотите запустить платный поиск по HUMO картам?');"
                                       class="btn btn-primary waves-effect waves-light me-5">
                                        <i class="fa fa-search-dollar align-middle font-size-16"></i> Запуск платного поиска по HUMO картам</a>
                                @endcan
                                @can('Тоггл авто всех контрактов')
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#toggle-auto" class="btn btn-primary">
                                        <i class="fa fa-toggle-off align-middle font-size-16"></i>
                                        Тоггл авто всех контрактов
                                    </button>
                                @endcan
                                @can('Обнулить долги всех контрактов')
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#zero-debit" class="btn btn-primary">
                                        <i class="fas fa-cubes align-middle font-size-16"></i>
                                        Обнулить долги всех контрактов
                                    </button>
                                @endcan
                                @can('Удалить все контракты')
                                    <button data-bs-toggle="modal" type="button" data-bs-target="#contract-delete" class="btn btn-primary">
                                        <i class="fa fa-trash align-middle font-size-16"></i>
                                        Удалить все контракты
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div id="toggle-auto" class="modal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('contracts.toggleAutoAll', request()->all()) }}" method="post">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Отключить/Включить авто контрактов</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="otpDisplay" class="mb-3">Ваш OTP код: <strong class="fs-5">{{$otp_code}}</strong></p>
                                        <input type="text" class="form-control input-mask mb-3" data-inputmask="'mask': '999999'" placeholder="______" id="value_auto" onkeyup="check_otp('value_auto','auto_button','{{ $otp_code }}')">

                                        <div class="form-group">
                                            <label for="actionSelect">Выберите действие:</label>
                                            <select name="action" class="form-select">
                                                <option value="disable">Отключить авто</option>
                                                <option value="enable">Включить авто</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="auto_button" disabled class="btn btn-primary">Подтвердить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="zero-debit" class="modal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('contracts.toZeroAll', request()->all()) }}" method="post">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Обнулить долги контрактов</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="otpDisplay" class="mb-3">Ваш OTP код: <strong class="fs-5">{{$otp_code}}</strong></p>
                                        <input type="text" data-inputmask="'mask': '999999'" placeholder="______"  class="form-control input-mask mb-3" id="value_zero" onkeyup="check_otp('value_zero','zero_button','{{ $otp_code }}')">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="zero_button" disabled class="btn btn-primary">Подтвердить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="contract-delete" class="modal fade" aria-hidden="true" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('contracts.deleteAll', request()->all()) }}" method="post">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Удалить контрактов</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="otpDisplay" class="mb-3">Ваш OTP код: <strong class="fs-5">{{$otp_code}}</strong></p>
                                        <input type="text" class="form-control input-mask mb-3" data-inputmask="'mask': '999999'" placeholder="______"  id="value_delete" onkeyup="check_otp('value_delete','delete_button','{{ $otp_code }}')">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="delete_button" disabled class="btn btn-primary">Подтвердить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @can('Создание контракта')
                        <a href="#" type="button"
                           data-bs-toggle="offcanvas" data-bs-target="#create_contract" aria-controls="offcanvasRight"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light me-2">
                            <i class="bx bx-bookmark-plus align-middle font-size-16"></i> Создать контракт</a>
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="create_contract"
                             aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Создать контракт</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="{{ route('contracts.store') }}" method="post">
                                    @csrf
                                    <div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">ПИНФЛ <sup
                                                        class="text-danger">*</sup></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="pinfl"
                                                       class="form-control input-mask @error('pinfl') is-invalid @enderror"
                                                       data-inputmask="'mask': '99999999999999'" im-insert="true"
                                                       required value="{{ old('pinfl') }}">
                                                @error('pinfl')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Лоан-ИД <sup
                                                        class="text-danger">*</sup></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="loan_id" required
                                                       maxlength="150" value="{{ old('loan_id') }}">
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Внешний-ИД</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="ext" maxlength="150"
                                                       value="{{ old('ext') }}">
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Мерчант <sup
                                                        class="text-danger">*</sup></label>
                                            <div class="col-sm-8">
                                                <select class="form-control select2" name="merchant_id">
                                                    @if(old('merchant_id'))
                                                        <option value="{{ old('merchant_id') }}" selected>
                                                        {{ \App\Models\Merchant::find(old('merchant_id'))->name }}
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Сумма долга</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" name="current_debt" required
                                                       value="{{ old('current_debt') }}">
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Автосписания</label>
                                            <input type="hidden" name="auto_operator" value="=">
                                            <div class="col-sm-8">
                                                <select name="auto" class="form-select">
                                                    <option value="1" @if(old('auto') == '1') selected @endif>On
                                                    </option>
                                                    <option value="0" @if(old('auto') == '0') selected @endif>Off
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Счет</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="account"
                                                       value="{{ old('account') }}">
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-5 pb-5">
                                            <div>
                                                <button type="button"
                                                        class="btn btn-secondary w-md float-end submitButton"
                                                        data-bs-dismiss="offcanvas" aria-label="Close">
                                                    Закрыть
                                                </button>
                                                <button type="submit"
                                                        class="mx-3 float-end btn btn-success waves-effect waves-light">
                                                    <i class="fa fa-user"></i> Создать
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan
                    @can('Импортировать контракты')
                        <a href="{{ route('contracts.download',request()->all()) }}" class="btn btn-outline-success btn-rounded waves-effect waves-light"><i class="fa fa-file-excel"> Экспортировать контракты</i></a>

                        <a href="#" type="button"
                           data-bs-toggle="offcanvas" data-bs-target="#import_contracts" aria-controls="offcanvasRight"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light">
                            <i class="fa fa-file-excel align-middle font-size-16"></i> Импортировать контракты</a>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="import_contracts"
                             aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Импортировать контракты</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <h5 class="font-size-15">Информация:</h5>
                                <p class="text-muted mb-4">
                                    Через импорт контрактов вы сможете обновить существующий контракт или создать новый, если он отсутствует.
                                    Учтите, что если введённый вами PINFL отсутствует в списке клиентов, контракт не будет создан.
                                </p>
                                <form action="{{ route('contracts.upload') }}" method="post"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="cols-sm-12 mb-4">
                                            <label class="form-label">Выберите файл, который
                                                вы хотите загрузить</label>
                                            <input type="file" class="form-control" name="file" required>
                                            @error('file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="cols-sm-12 mb-4">
                                            <label>Мерчант</label>
                                            <select class="form-select select-file" name="merchant_id" required></select>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-success w-md col-sm-12"><i
                                                        class="fa fa-file-excel"></i> Отправить
                                            </button>
                                        </div>
                                       <div>
                                           <h5 class="font-size-15 mt-4">Образец:</h5>
                                           <a class="btn btn-success w-md col-sm-12" href="{{route('contracts.example.download')}}">
                                               <i class="fa fa-file-download"></i>
                                               Скачать</a>
                                       </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card p-0">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne">
                            Поиск <sup class="badge badge-soft-primary" style="margin-right: 15px">{{ number_format($contracts->total()) }}</sup>
                        </button>

                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body text-muted">
                            <form action="" method="GET">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-3 mb-3">
                                        <label for="pinfl" class="form-label">ПИНФЛ</label>
                                        <input type="text" name="pinfl" id="pinfl" class="form-control" maxlength="14" value="{{ request()->pinfl }}">
                                    </div>
                                    <div class="col-sm-12 col-lg-3 mb-3">
                                        <label for="loan_id" class="form-label">Лоан-ИД</label>
                                        <input type="text" name="loan_id" id="loan_id" class="form-control" maxlength="150" value="{{ request()->loan_id }}">
                                    </div>
                                    <div class="col-sm-12 col-lg-3 mb-3">
                                        <label for="ext" class="form-label">Внешний-ИД</label>
                                        <input type="text" name="ext" id="ext" class="form-control" maxlength="150" value="{{ request()->ext }}">
                                    </div>
                                    <div class="col-sm-12 col-lg-3 mb-3">
                                        <label for="status_auto" class="form-label">Статус автосписания</label>
                                        <select name="auto" id="status_auto" class="form-select">
                                            <option value="">Все</option>
                                            <option value="0" @if(request()->auto == '0') selected @endif>Отключен</option>
                                            <option value="1" @if(request()->auto == '1') selected @endif>Включен</option>
                                        </select>
                                        <input type="hidden" name="auto_operator" value="=">
                                    </div>
                                    <div class="col-sm-12 col-lg-3 mb-3">
                                        <label for="merchant_id" class="form-label">Выберите мерчанта</label> <br>
                                        <select class="form-select select-search" id="merchant_id" name="merchant_id" style="width: 100%">
                                            @if(request()->merchant_id)
                                                <option value="{{ request()->merchant_id }}" selected>
                                                    {{ \App\Models\Merchant::find(request()->merchant_id)->name }}
                                                </option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="merchant_id_operator" value="=">
                                    </div>
                                        @if(\App\Services\Helpers\Check::isAdmin())
                                            <div class="col-sm-12 col-lg-3 mb-3">
                                                <label class="form-label" for="partner_id">Выберите партнера</label>
                                                <select class="form-select select-search-partner" id="partner_id" name="partner_id" style="width: 100%">
                                                    @if(request()->partner_id)
                                                        <option value="{{ request()->partner_id }}" selected>
                                                        {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                                    @endif
                                                </select>
                                                <input type="hidden" name="partner_id_operator" value="=">
                                            </div>
                                            <div class="col-sm-12 col-lg-3 mb-3">
                                                <div class="form-group row align-items-center">
                                                        <div class="col-sm-12 col-lg-2 w-100">
                                                            <label class="form-label">Сумма долга</label>
                                                        </div>
                                                        <div class="col-sm-12 col-lg-2">
                                                            <select class="form-control" name="current_debt_operator"
                                                                    onchange="
                                                                                if(this.value == 'between'){
                                                                                document.getElementById('current_debt_pair').style.display = 'block';
                                                                                } else {
                                                                                document.getElementById('current_debt_pair').style.display = 'none';
                                                                                }
                                                                                ">
                                                                <option value="" {{ request()->current_debtt_operator == '=' ? 'selected':'' }}> = </option>
                                                                <option value=">" {{ request()->current_debt_operator == '>' ? 'selected':'' }}> > </option>
                                                                <option value="<" {{ request()->current_debt_operator == '<' ? 'selected':'' }}> < </option>
                                                                <option value="between" {{ request()->current_debt_operator == 'between' ? 'selected':'' }}> От .. до .. </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-12 col-lg-5 mx-2">
                                                            <input class="form-control" type="number" name="current_debt" value="{{ old('current_debt',request()->current_debt??'') }}">
                                                        </div>
                                                        <div class="col-sm-12 col-lg-4" id="current_debt_pair" style="display: {{ request()->current_debt_operator == 'between' ? 'block':'none'}}">
                                                            <input class="form-control" type="number" name="current_debt_pair" value="{{ old('current_debt_pair',request()->current_debt_pair??'') }}">
                                                        </div>
                                                    </div>
                                            </div>
                                        @else
                                            <div class="col-sm-12 col-lg-3 mb-3">
                                                <div class="form-group row align-items-center">
                                                        <div class="col-sm-12 col-lg-2 w-100">
                                                            <label class="form-label">Сумма долга</label>
                                                        </div>
                                                        <div class="col-sm-12 col-lg-2">
                                                            <select class="form-control" name="current_debt_operator"
                                                                    onchange="
                                                                                if(this.value == 'between'){
                                                                                document.getElementById('current_debt_pair').style.display = 'block';
                                                                                } else {
                                                                                document.getElementById('current_debt_pair').style.display = 'none';
                                                                                }
                                                                                ">
                                                                <option value="" {{ request()->current_debtt_operator == '=' ? 'selected':'' }}> = </option>
                                                                <option value=">" {{ request()->current_debt_operator == '>' ? 'selected':'' }}> > </option>
                                                                <option value="<" {{ request()->current_debt_operator == '<' ? 'selected':'' }}> < </option>
                                                                <option value="between" {{ request()->current_debt_operator == 'between' ? 'selected':'' }}> От .. до .. </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-12 col-lg-5 mx-2">
                                                            <input class="form-control" type="number" name="current_debt" value="{{ old('current_debt',request()->current_debt??'') }}">
                                                        </div>
                                                        <div class="col-sm-12 col-lg-4" id="current_debt_pair" style="display: {{ request()->current_debt_operator == 'between' ? 'block':'none'}}">
                                                            <input class="form-control" type="number" name="current_debt_pair" value="{{ old('current_debt_pair',request()->current_debt_pair??'') }}">
                                                        </div>
                                                    </div>
                                            </div>
                                        @endif
                                        <div class="col-sm-12 col-lg-2 mb-3">
                                            <div class="btn-group w-100 mt-4" role="group">
                                                <button type="submit" class="btn-rounded btn btn-primary">
                                                    <i class="fas fa-search font-size-14"></i>
                                                </button>

                                                <a href="{{ route('contracts.index') }}" class="btn-rounded btn btn-warning">
                                                    <i class="fas fa-sync font-size-14"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(\Illuminate\Support\Facades\Cache::has('contracts_import_new_'.auth()->user()->partner_id))
        <div class="alert alert-warning alert-dismissible fade show w-auto me-2" role="alert">
            Новые контракты импортируются
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if(\Illuminate\Support\Facades\Cache::has('contracts_import_update_'.auth()->user()->partner_id))
        <div class="alert alert-warning alert-dismissible fade show w-auto" role="alert">
            Контракты обновляется
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="card">
                <div class="card-body">
                    <x-alert-success/>
                    @if(session('validationErrors'))
                        <div class="alert alert-warning mt-3">
                            <strong>Ошибки валидации:</strong>
                            <button type="button" class="btn-close float-end" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            <ul>
                                @foreach(session('validationErrors') as $error)
                                    <li>
                                        <strong>Строка {{ $error['row'] }}:</strong>
                                        <ul>
                                            @foreach($error['errors'] as $errorMessage)
                                                <li>{{ $errorMessage }}</li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table align-middle table-check myDt">
                            <thead class="table-light">
                            <tr>
                                @if(auth()->user()->is_admin)
                                    <th class="align-middle">Партнер</th>
                                @endif
                                <th class="align-middle">ПИНФЛ</th>
                                <th class="align-middle">LOAN ID</th>
                                <th class="align-middle">Внешний-ИД</th>
                                <th class="align-middle">Мерчант</th>
                                <th class="align-middle">Сумма долга</th>
                                <th class="align-middle">Авто</th>
                                <th class="text-center">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    @if(\App\Services\Helpers\Check::isAdmin())
                                        <td class="fw-muted">
                                            {{ $contract->partner->name ?? ''}}
                                        </td>
                                    @endif
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            @can('Просмотр контракта')
                                                <a href="{{ route('contracts.show',$contract->id) }}">{{ $contract->pinfl }}</a>
                                            @else
                                                {{ $contract->pinfl }}
                                            @endcan
                                        </h6>
                                        @if(auth()->user()->is_admin == false)
                                            <p class="text-muted mb-0 font-size-10">
                                                {{ $contract->client ? $contract->client->fio() : '-' }}
                                            </p>
                                        @endif
                                    </td>
                                    <td>
                                        @can('Просмотр контракта')
                                            <a href="{{ route('contracts.show',$contract->id) }}">{{ $contract->loan_id }}</a>
                                        @else
                                            {{ $contract->loan_id }}
                                        @endcan
                                    </td>
                                    <td>
                                        {{ $contract->ext }}
                                    </td>
                                    <td>
                                        {{ $contract->merchant_name }}
                                    </td>
                                    <td>
                                        {{ number_format($contract->current_debt/100, 2,'.') }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $contract->auto ? 'bg-success' : 'bg-danger' }}" style="cursor: pointer;" onclick="toggleAutoContract(this,{{ $contract->id }})" data-auto="{{ $contract->auto }}">
                                            <i class="fas fa-{{ $contract->auto ? 'check' : 'ban' }}"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            @can('Редактирование контракта')
                                                <button type="button"
                                                        class="btn btn-outline-primary btn-sm"
                                                        data-bs-toggle="offcanvas"
                                                        data-bs-target="#contract_edit_{{ $contract->id }}"
                                                        aria-controls="offcanvasRight">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            @endcan
                                            @can('Удаление контракта')
                                                <form action="{{ route('contracts.destroy',$contract->id) }}"
                                                      method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm ms-2 submitButtonConfirm">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @can('Редактирование контракта')
                                    <div class="offcanvas offcanvas-end" tabindex="-1"
                                         id="contract_edit_{{ $contract->id }}"
                                         aria-labelledby="offcanvasRightLabel" aria-hidden="true"
                                         style="visibility: hidden;">
                                        <div class="offcanvas-header">
                                            <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Редактирование
                                                контракта</h5>
                                            <button type="button" class="btn-close text-reset"
                                                    data-bs-dismiss="offcanvas"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <form action="{{ route('contracts.update',$contract->id) }}" method="post">
                                                @method('PUT')
                                                @csrf
                                                <div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">ПИНФЛ <sup
                                                                    class="text-danger">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="pinfl"
                                                                   class="form-control input-mask @error('pinfl') is-invalid @enderror"
                                                                   data-inputmask="'mask': '99999999999999'"
                                                                   im-insert="true"
                                                                   required value="{{ $contract->pinfl }}">
                                                            @error('pinfl')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Лоан-ИД <sup
                                                                    class="text-danger">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text"
                                                                   class="form-control @error('loan_id') is-invalid @enderror"
                                                                   name="loan_id" required maxlength="150"
                                                                   value="{{ $contract->loan_id }}">
                                                        </div>
                                                        @error('loan_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Внешний-ИД</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="ext"
                                                                   maxlength="150" value="{{ $contract->ext }}">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Мерчант <sup
                                                                    class="text-danger">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text" value="{{ $contract->merchant_name ?? ''}}" readonly class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Сумма долга</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control numberFormat"
                                                                   name="current_debt" required
                                                                   value="{{ number_format($contract->current_debt/100,2,'.')}}">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Автосписаниe</label>
                                                        <div class="col-sm-8">
                                                            <select name="auto" class="form-select">
                                                                <option value="1"
                                                                        @if($contract->auto == true) selected @endif>On
                                                                </option>
                                                                <option value="0"
                                                                        @if($contract->auto === false) selected @endif>
                                                                    Off
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 mb-5 pb-5">
                                                        <div>
                                                            <button type="button"
                                                                    class="btn btn-secondary w-md float-end submitButton"
                                                                    data-bs-dismiss="offcanvas" aria-label="Close">
                                                                Закрыт
                                                            </button>
                                                            <button type="submit"
                                                                    class="mx-3 float-end btn btn-success waves-effect waves-light">
                                                                <i class="fa fa-user"></i> Обновить
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endcan
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $contracts->withQueryString()->links() }}
                </div>
            </div>
        </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/form-mask.init.js') }}"></script>


    <script type="application/javascript">
        $(document).ready(function () {
            // Initialize Select2 with AJAX options
            $('.select2').select2({
                placeholder: 'Поиск мерчанта...',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("merchants.search") }}',
                    dataType: 'json',
                    method: 'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (client) {
                                return {
                                    id: client.id,
                                    text: client.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                dropdownParent: $('#create_contract') // Adjust this selector to your off-canvas container
            });

            $('.select-file').select2({
                placeholder: 'Поиск мерчанта...',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("merchants.search") }}',
                    dataType: 'json',
                    method: 'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (client) {
                                return {
                                    id: client.id,
                                    text: client.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                dropdownParent: $('#import_contracts') // Adjust this selector to your off-canvas container
            });

            $('.select-search').select2({
                placeholder: 'Поиск мерчанта...',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("merchants.search") }}',
                    dataType: 'json',
                    method: 'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (client) {
                                return {
                                    id: client.id,
                                    text: client.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
            $('.select-search-partner').select2({
                placeholder: 'Поиск партнера...',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("partners.search") }}',
                    dataType: 'json',
                    method: 'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (partner) {
                                return {
                                    id: partner.id,
                                    text: partner.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });
        function check_otp(input_id, button_id, otp) {
            if ($("#"+input_id).val() === otp)
                $("#"+button_id).attr('disabled',false)
            else
                $("#"+button_id).attr('disabled',true)
        }
        function toggleAutoContract(element, contractId) {
            const currentAutoStatus = $(element).data('auto');

            // Сохраняем текущий элемент и его HTML для восстановления
            const originalBadge = $(element);
            const originalBadgeHtml = originalBadge.prop('outerHTML');

            // Заменяем badge на спиннер
            originalBadge.replaceWith('<i class="fas fa-spinner fa-spin" id="loading-spinner"></i>');

            $.ajax({
                url: `/contract/${contractId}/toggle-auto`,
                type: 'POST',
                data: {
                    auto: !currentAutoStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const newStatus = !currentAutoStatus;

                        // Восстанавливаем badge с обновлённым состоянием
                        $('#loading-spinner').replaceWith(`
                    <span class="badge bg-${newStatus ? 'success' : 'danger'}" style="cursor: pointer;" onclick="toggleAutoContract(this,`+contractId+`)" data-auto="${newStatus}">
                        <i class="fas fa-${newStatus ? 'check' : 'ban'}"></i>
                    </span>
                `);
                    } else {
                        alert('Не удалось изменить статус.');

                        // Восстанавливаем оригинальный badge
                        $('#loading-spinner').replaceWith(originalBadgeHtml);
                    }
                },
                error: function(xhr) {
                    alert('Произошла ошибка. Попробуйте позже.');
                    console.error(xhr.responseText);

                    // Восстанавливаем оригинальный badge
                    $('#loading-spinner').replaceWith(originalBadgeHtml);
                }
            });
        }
    </script>
@endsection
