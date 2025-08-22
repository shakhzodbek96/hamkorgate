@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Партнеры</h4>

                <div class="page-title-right btn-group-sm">
                    <a href="#" type="button"
                       data-bs-toggle="offcanvas" data-bs-target="#openActions" aria-controls="offcanvasRight"
                       class="btn btn-outline-dark btn-rounded waves-effect waves-light me-2">
                        <i class="bx bx-cog align-middle font-size-16"></i> Активности</a>
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="openActions"
                         aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                        <div class="offcanvas-header">
                            <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Активности для партнеров</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <div class="btn-group-vertical gap-4 w-100">
                            </div>
                        </div>
                    </div>
                    @can('Создать партнера')
                        <a href="{{ route('partners.create') }}" type="button"
                           class="btn btn-outline-success waves-effect waves-light">
                            <i class="bx bx-bookmark-plus align-middle font-size-16"></i> Создать партнера</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-success">{{ $partners->total() }}</sup>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-2">
                                <label>ИНН</label>
                                <input type="text" name="inn" class="form-control" value="{{ request()->inn }}">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Наименование</label>
                                <input type="text" name="name" class="form-control" value="{{ request()->name }}">
                                <input type="hidden" name="name_operator" value="like">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Телефон</label>
                                <input type="text" name="phone" class="form-control" value="{{ request()->phone }}">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Статус</label>
                                <select name="is_active" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="true"  @if(request()->is_active == 'true') selected @endif >Актив ✅</option>
                                    <option value="false" @if(request()->is_active == 'false') selected @endif >Отключено ❌</option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Автосписание</label>
                                <select name="auto" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="true" @if(request()->auto == 'true') selected @endif >Актив ✅</option>
                                    <option value="false" @if(request()->auto== 'false') selected @endif >Отключено ❌</option>
                                </select>
                            </div>
                            <input type="hidden" name="is_active_operator" value="=">
                            <input type="hidden" name="auto_operator" value="=">
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('partners.index') }}" class="btn btn-warning btn-rounded">
                                        <i class="fas fa-sync font-size-14"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <x-alert-success/>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">ID#</th>              <!-- inn -->
                                <th class="align-middle">ИНН</th>              <!-- inn -->
                                <th class="align-middle">Наименование</th>      <!-- name -->
                                <th class="align-middle">Телефон</th>           <!-- phone -->
                                <th class="align-middle" colspan="2">Комиссия SV/Humo</th>          <!-- commission -->
                                <th class="align-middle">Статус</th>           <!-- is_active -->
                                <th class="align-middle text-center">Автосписание</th>    <!-- auto -->
                                <th class="align-middle">Конфигурация</th>      <!-- config -->
                                <th class="align-middle">Действие</th>          <!-- Action -->
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($partners as $partner)
                                <tr>
                                    <td>{{ $partner->id }}</td>
                                    <td class="text-body fw-bold">
                                        {{ $partner->inn }}
                                    </td>
                                    <td>
                                        {{ $partner->name }}
                                    </td>
                                    <td>
                                        {{ \App\Services\Helpers\Helper::phoneShowFormatting($partner->phone) }}
                                    </td>
                                    <td class="fw-bold text-muted">
                                        {{ $partner->commission }} %
                                    </td>
                                    <td>
                                        {{ $partner->commission_humo }} %
                                    </td>
                                    <td>
                                        <div class="btn-group dropend">
                                            <span
                                                class="badge badge-pill badge-soft-{{ $partner->is_active ? 'success':'danger' }} font-size-12"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $partner->is_active ? 'Актив' : 'Отключено' }}
                                        </span>
                                            @can('Редактировать партнера')
                                                <div class="dropdown-menu w-sm" style="">
                                                    @if($partner->is_active)
                                                        <a class="dropdown-item"
                                                           href="{{ route('partners.toggleStatus',['partner_id' => $partner->id,'is_active' => false]) }}">
                                                            <i class="fas fa-power-off text-danger font-size-16"></i>
                                                            Отключить
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item"
                                                           href="{{ route('partners.toggleStatus',['partner_id' => $partner->id,'is_active' => true]) }}">
                                                            <i class="fas fa-power-off text-success font-size-16"></i>
                                                            Активироват
                                                        </a>
                                                    @endif
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group dropend">
                                            <span type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-power-off text-{{ $partner->auto ? 'success':'danger' }} font-size-16"></i>
                                            </span>
                                            @can('Редактировать партнера')
                                                <div class="dropdown-menu w-sm" style="">
                                                    @if($partner->auto)
                                                        <a class="dropdown-item"
                                                           href="{{ route('partners.toggleAuto',['partner_id' => $partner->id,'auto' => false]) }}">
                                                            <i class="fas fa-power-off text-danger font-size-16"></i>
                                                            Отключить
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item"
                                                           href="{{ route('partners.toggleAuto',['partner_id' => $partner->id,'auto' => true]) }}">
                                                            <i class="fas fa-power-off text-success font-size-16"></i>
                                                            Включить
                                                        </a>
                                                    @endif
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="offcanvas" data-bs-target="#details_{{ $partner->id }}"
                                                    aria-controls="offcanvasRight">
                                                <i class="fa fa-cogs"></i> Детали
                                            </button>
                                            @can('Добавить пользователя в партнера')
                                                <button type="button" class="btn btn-outline-success btn-sm "
                                                        data-bs-toggle="offcanvas" data-bs-target="#add_user_{{ $partner->id }}"
                                                        aria-controls="offcanvasRight">
                                                    <i class="fa fa-plus"></i> Добавить пользователя
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex me-2">
                                            @can('Просмотр партнера')
                                                <a href="{{ route('partners.show',$partner->id) }}"
                                                   class="btn border-0 btn-outline-info mx-2 btn-rounded waves-effect waves-light btn-sm"><i
                                                        class="mdi mdi-eye font-size-18"></i></a>
                                            @endcan
                                            @can('Редактировать партнера')
                                                <a href="{{ route('partners.edit',$partner->id) }}"
                                                   class="btn border-0 btn-outline-primary mx-2 btn-rounded waves-effect waves-light btn-sm"><i
                                                        class="mdi mdi-pencil font-size-18"></i></a>
                                            @endcan
                                            @can('Удалить партнера')
                                                <a href="javascript:void(0);"
                                                   class="btn border-0 btn-outline-danger mx-2 btn-rounded waves-effect waves-light btn-sm"><i
                                                        class="mdi mdi-delete font-size-18"></i></a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
{{--                                @php $partner->config = json_decode($partner->config,1); @endphp--}}
                                <div class="offcanvas offcanvas-end" tabindex="-1" id="details_{{ $partner->id }}"
                                     aria-labelledby="offcanvasRightLabel" style="visibility: hidden;"
                                     aria-hidden="true">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel">Конфигурация для <span
                                                class="fw-bold text-primary">{{ $partner->name }}</span></h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                aria-label="Закрыть"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <form action="{{ route('partners.configurations',$partner->id) }}"
                                              id="partner-config-form_{{ $partner->id }}"
                                              method="post">
                                            @method('PUT')
                                            <div class="row">
                                                @csrf
                                                <h5 class="fw-bold text-primary">Аутентификация</h5>
                                                <div class="mb-3 col-lg-6 col-sm-12">
                                                    <label class="form-label">Логин</label>
                                                    <input type="text" class="form-control"
                                                           name="config[auth][username]"
                                                           value="{{ $partner->config['auth']['username'] }}">
                                                </div>
                                                <div class="mb-3 col-lg-6 col-sm-12">
                                                    <label class="form-label">Пароль</label>
                                                    <input type="text" class="form-control"
                                                           name="config[auth][password]"
                                                           value="{{ $partner->config['auth']['password'] }}">
                                                </div>
                                                <div class="mb-3 col-lg-12 col-sm-12">
                                                    <label class="form-label">Токен</label>
                                                    <input type="text" class="form-control" name="config[auth][token]"
                                                           value="{{ $partner->config['auth']['token'] }}">
                                                </div>
                                                <div class="mb-3 col-lg-12 col-sm-12">
                                                    <label class="form-label">Ограничение запросов</label>
                                                    <input type="number" class="form-control" name="config[auth][rate_limit]"
                                                           value="{{ $partner->config['auth']['rate_limit'] ?? '' }}">
                                                </div>
                                                <hr>
                                                <h5 class="pt-2 fw-bold text-primary">Вебхук</h5>
                                                <div class="mb-3 col-lg-9 col-sm-8">
                                                    <label class="form-label">Хост</label>
                                                    <input type="url" class="form-control" name="config[webhook][host]"
                                                           value="{{ $partner->config['webhook']['host'] }}">
                                                </div>
                                                <div class="mb-3 col-lg-3 col-sm-4">
                                                    <label class="form-label">Статус</label>
                                                    <select name="config[webhook][status]" class="form-select">
                                                        <option value="1"
                                                                @if($partner->config['webhook']['status'] == true) selected @endif>
                                                            On
                                                        </option>
                                                        <option value="0"
                                                                @if($partner->config['webhook']['status'] == false) selected @endif>
                                                            Off
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-lg-12">
                                                    <label class="form-label">Токен</label>
                                                    <input type="text" class="form-control"
                                                           name="config[webhook][token]"
                                                           value="{{ $partner->config['webhook']['token'] }}">
                                                </div>
                                                <hr>
                                                <h5 class="pt-2 fw-bold text-primary">Flexsoft</h5>
                                                <div class="mb-3 col-lg-9 col-sm-8">
                                                    <label class="form-label">Хост</label>
                                                    <input type="url" class="form-control" name="config[flex][host]"
                                                           value="{{ $partner->config['flex']['host'] ?? '' }}">
                                                </div>
                                                <div class="mb-3 col-lg-3 col-sm-4">
                                                    <label class="form-label">Статус</label>
                                                    <select name="config[flex][status]" class="form-select">
                                                        <option value="0"
                                                                @if(isset($partner->config['flex']['status']) && $partner->config['flex']['status'] == false) selected @endif>
                                                            Off
                                                        </option>
                                                        <option value="1"
                                                                @if(isset($partner->config['flex']['status']) && $partner->config['flex']['status'] == true) selected @endif>
                                                            On
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-lg-12">
                                                    <label class="form-label">Токен</label>
                                                    <input type="text" class="form-control"
                                                           name="config[flex][token]"
                                                           value="{{ $partner->config['flex']['token'] ?? '' }}">
                                                </div>
                                                <hr>
                                                <h5 class="pt-2 fw-bold text-primary">СМС</h5>
                                                <div class="mb-3 col-lg-9 col-sm-12">
                                                    <label class="form-label">Логин</label>
                                                    <input type="text" class="form-control"
                                                           name="config[sms][username]"
                                                           value="{{ $partner->config['sms']['username']??'' }}">
                                                </div>
                                                <div class="mb-3 col-lg-3 col-sm-4">
                                                    <label class="form-label">Статус</label>
                                                    <select name="config[sms][status]" class="form-select">
                                                        <option value="1"
                                                                @if($partner->config['sms']['status'] == true) selected @endif>On
                                                        </option>
                                                        <option value="0"
                                                                @if($partner->config['sms']['status'] == false) selected @endif>
                                                            Off
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-lg-12 col-sm-12">
                                                    <label class="form-label">Пароль</label>
                                                    <input type="text" class="form-control"
                                                           name="config[sms][password]"
                                                           value="{{ $partner->config['sms']['password']??'' }}">
                                                </div>
                                                <hr>
                                                <h5 class="pt-2 fw-bold text-primary">Уведомления в Telegram</h5>
                                                <div class="mb-3 col-lg-6">
                                                    <label class="form-label">Канал платежей</label>
                                                    <input type="text" class="form-control"
                                                           name="config[notifications][payment]"
                                                           value="{{ $partner->config['notifications']['payment'] }}">
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label class="form-label">Канал предупреждений</label>
                                                    <input type="text" class="form-control"
                                                           name="config[notifications][warnings]"
                                                           value="{{ $partner->config['notifications']['warnings'] }}">
                                                </div>
                                                <hr>
                                                <h5 class="pt-2 fw-bold text-primary mb-3">Право на платные услуги</h5>
                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Статистика контрактов</label>
                                                    <input type="hidden" name="config[card_service][contract_stats]" value="0">
                                                    <div class="form-check form-switch p-0 mt-2">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               id="contract_stats_switch_{{$partner->id}}"
                                                               name="config[card_service][contract_stats]"
                                                               value="1"
                                                               switch="bool"
                                                               @if( ($partner->config['card_service']['contract_stats'] ?? 0) == 1 ) checked @endif />
                                                        <label class="form-check-label" for="contract_stats_switch_{{$partner->id}}"
                                                               data-on-label="Вкл" data-off-label="Выкл">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Контракты</label>
                                                    <input type="hidden" name="config[card_service][contracts]" value="0">
                                                    <div class="form-check form-switch p-0 mt-2">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               id="contracts_switch_{{$partner->id}}"
                                                               name="config[card_service][contracts]"
                                                               value="1"
                                                               switch="bool"
                                                               @if( ($partner->config['card_service']['contracts'] ?? 0) == 1 ) checked @endif />
                                                        <label class="form-check-label" for="contracts_switch_{{$partner->id}}"
                                                               data-on-label="Вкл" data-off-label="Выкл">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Контракт</label>
                                                    <input type="hidden" name="config[card_service][contract]" value="0">
                                                    <div class="form-check form-switch p-0 mt-2">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               id="contract_switch_{{$partner->id}}"
                                                               name="config[card_service][contract]"
                                                               value="1"
                                                               switch="bool"
                                                               @if( ($partner->config['card_service']['contract'] ?? 0) == 1 ) checked @endif />
                                                        <label class="form-check-label" for="contract_switch_{{$partner->id}}"
                                                               data-on-label="Вкл" data-off-label="Выкл">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-6">
                                                    <label class="form-label">Е-Гов</label>
                                                    <input type="hidden" name="config[e_gov][service]" value="0">
                                                    <div class="form-check form-switch p-0 mt-2">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               id="service_switch_{{$partner->id}}"
                                                               name="config[e_gov][service]"
                                                               value="1"
                                                               switch="bool"
                                                               @if( ($partner->config['e_gov']['service'] ?? 0) == 1 ) checked @endif />
                                                        <label class="form-check-label" for="service_switch_{{$partner->id}}"
                                                               data-on-label="Вкл" data-off-label="Выкл">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer p-5 float-end" >
                                        <button type="submit" form="partner-config-form_{{$partner->id}}" class="btn btn-outline-success mx-2 w-md"><i
                                                    class="fa fa-save"></i> Сохранить
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary w-md"
                                                data-bs-dismiss="offcanvas"
                                                aria-label="Закрыть">Закрыть
                                        </button>
                                    </div>
                                </div>
                                @can('Добавить пользователя в партнера')
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="add_user_{{ $partner->id }}"
                                         aria-labelledby="offcanvasRightLabel" style="visibility: hidden;"
                                         aria-hidden="true">
                                        <div class="offcanvas-header">
                                            <h5 id="offcanvasRightLabel">Добавить пользователя для <span
                                                    class="fw-bold text-primary">{{ $partner->name }}</span></h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                    aria-label="Закрыть"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <form action="{{ route('partners.add-user',$partner->id) }}"
                                                  method="post">
                                                <div class="row">
                                                    @csrf
                                                    <div class="mb-3 col-lg-12 col-sm-12">
                                                        <label class="form-label">ФИО</label>
                                                        <input type="text" class="form-control"
                                                               name="name">
                                                    </div>
                                                    <div class="mb-3 col-lg-12 col-sm-12">
                                                        <label class="form-label">Телефон</label>
                                                        <input type="text" class="form-control input-mask @error('phone') is-invalid @enderror"
                                                               value="{{ old('phone') }}" id="phone" name="phone" autofocus required
                                                               data-inputmask="'mask': '99-999-99-99'" im-insert="true">
                                                    </div>
                                                    <div class="mb-3 col-lg-12 col-sm-12">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control"
                                                               name="email">
                                                    </div>
                                                    <div class="mb-3 col-lg-12 col-sm-12">
                                                        <label class="form-label" for="passwordInput_{{ $partner->id }}">Пароль</label>
                                                        <div class="input-group">
                                                            <input type="text" id="passwordInput_{{ $partner->id }}"
                                                                   class="form-control passwordInput @error('password') is-invalid @enderror"
                                                                   name="password"
                                                                   placeholder="Введите пароль">
                                                            <button class="btn btn-light generatePassword" type="button"
                                                                    data-target="#passwordInput_{{ $partner->id }}" aria-label="Generate password">
                                                                Генерировать
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 col-lg-12 col-sm-12">
                                                        <label class="form-label">Выбрать роль</label>
                                                        <select class="select2 form-control select2-multiple" multiple="multiple" name="roles[]"
                                                                data-placeholder="Выбрать ...">
                                                            @foreach($roles->where('partner_id' ,$partner->id) as $role)
                                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="pt-3 float-end">
                                                        <button type="submit" class="btn btn-success mx-2 w-md"><i
                                                                class="fa fa-plus"></i> Добавить
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary w-md"
                                                                data-bs-dismiss="offcanvas"
                                                                aria-label="Закрыть">Закрыть
                                                        </button>
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
                    <div class="pt-3">
                        {{ $partners->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/form-mask.init.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.generatePassword').on('click', function () {
                const targetInput = $(this).data('target');
                const generatePassword = (length = 12) => {
                    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$&';
                    let password = '';
                    for (let i = 0; i < length; i++) {
                        password += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    return password;
                };

                const password = generatePassword();
                $(targetInput).val(password);
            });
        });
    </script>
@endsection
