@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css"/>
    <style>

    </style>
@endsection
@section('content')
    <div class="row mb-2">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Информация о партнере</h4>
                <div class="page-title-right px-3">
                    <form id="date-filter-form" class="row gx-1 align-items-center">
                        <div class="col-auto">
                            <select class="form-select form-select-sm" id="date_operator" name="date_operator">
                                <option value="=" {{ request()->date_operator == '=' ? 'selected':'' }}>=</option>
                                <option value=">" {{ request()->date_operator == '>' ? 'selected':'' }}>&gt;</option>
                                <option value="<" {{ request()->date_operator == '<' ? 'selected':'' }}>&lt;</option>
                                <option value="between" {{ request()->date_operator == 'between' ? 'selected':'' }}>
                                    &#8596;
                                </option>
                            </select>
                        </div>

                        <div class="col-auto">
                            <input type="date" id="date" name="date" class="form-control form-control-sm"
                                   value="{{ old('date', request()->date ?? '') }}">
                        </div>

                        <div class="col-auto" id="date_pair_container"
                             style="display: {{ request()->date_operator == 'between' ? 'block' : 'none' }}">
                            <input type="date" id="date_pair" name="date_pair" class="form-control form-control-sm"
                                value="{{ old('date_pair', request()->date_pair ?? '') }}" >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-4">
                            <i class="mdi mdi-account-circle text-primary h1"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="text-muted">
                                <h5>{{ $partner->name }}</h5>
                                <p class="mb-1">INN: {{ $partner->inn }}</p>
                                <p class="mb-0">
                                    Телефон: <a
                                        href="tel:{{ \App\Services\Helpers\Helper::phoneFormat($partner->phone) }}"
                                        class="mb-0">
                                        {{ \App\Services\Helpers\Helper::phoneShowFormatting($partner->phone) }}
                                    </a>
                                </p>
                            </div>

                        </div>

                        <div class="ms-2">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="offcanvas" data-bs-target="#config" aria-controls="offcanvasRight">
                                <i class="bx bxs-cog align-middle me-1"></i> Конфигурация
                            </button>
                        </div>
                    </div>
                </div>
                <!-- filter results-->
                <div id="filter-results">
                    <div class="card-body border-top">
                        <div class="row">
                            @forelse($transactionsByType as $transaction)
                                <div class="col-sm-6">
                                    <div class="{{$transaction->type === 'user'?'text-sm-end mt-4 mt-sm-0':''}}">
                                        <p class="text-muted mb-2">{{$transaction->type === 'system'?'Система':'Пользователь'}}</p>
                                        <h5> {{ number_format(($transaction->total_amount ?? 0)/100, 2, '.', ',') }}
                                            <span
                                                class="badge badge-soft-info">{{number_format($transaction->total_count)}}</span>
                                        </h5>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center mb-2">Информация не найдены</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card-body border-top">
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <p class="text-muted mb-2">Текущий баланс (месяц)</p>
                                <h5> {{ number_format(($statCurrent?->total_amount ?? 0)/100, 2, '.', ',') }}
                                    <span
                                        class="badge bg-{{data_get($statCurrent?->stats, 'total_amount_percent', 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent(data_get($statCurrent?->stats, 'total_amount_percent', '0.00%'))}}</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end mt-4 mt-sm-0">
                                <p class="text-muted mb-2">Предыдущий баланс (месяц)</p>
                                <h5> {{number_format($statPrev?->total_amount/100, 2, '.', ',')}}
                                    <span
                                        class="badge bg-{{data_get($statPrev?->stats, 'total_amount_percent', 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent(data_get($statPrev?->stats, 'total_amount_percent', '0.00%'))}}</span>
                                </h5>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border-top">
                    <p class="text-muted mb-4"></p>
                    <div class="text-center">
                        <div class="row">
                            <div class="col-sm-4" role="button"
                                 onclick="window.location.href = '{{route('merchants.index', ['partner_id' => $partner->id])}}'">
                                <div>
                                    <div class="font-size-24 text-primary mb-2">
                                        <i class="fas fa-building"></i>
                                    </div>

                                    <p class="text-muted mb-2">Мерчанты</p>
                                    <h5>{{ number_format($partner->merchants_count) }} <small
                                            class="text-muted">кол-во</small></h5>
                                </div>
                            </div>
                            <div class="col-sm-4" role="button"
                                 onclick="window.location.href = '{{route('clients.index', ['partner_id' => $partner->id])}}'">
                                <div class="mt-4 mt-sm-0">
                                    <div class="font-size-24 text-primary mb-2">
                                        <i class="fas fa-users"></i>
                                    </div>

                                    <p class="text-muted mb-2">Клиенты</p>
                                    <h5>{{ number_format($partner->clients_count) }} <small
                                            class="text-muted">кол-во</small></h5>
                                </div>
                            </div>
                            <div class="col-sm-4"
                                 onclick="window.location.href = '{{route('contracts.index', ['partner_id' => $partner->id])}}'"
                                 role="button">
                                <div class="mt-4 mt-sm-0">
                                    <div class="font-size-24 text-primary mb-2">
                                        <i class="fas fa-file-contract"></i>
                                    </div>

                                    <p class="text-muted mb-2">Контакты</p>
                                    <h5>{{ number_format($contracts->count) }} <small class="text-muted">кол-во</small>
                                    </h5>
                                    <h5>{{ number_format($contracts->current_debt / 100 / 1_000_000, 2, '.', ',') }}
                                        <small class="text-muted">млн</small></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-sm-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3 align-self-center">
                                    <img src="https://uzcard.uz/favicon.png" class=" mb-0" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2">Узкард</p>
                                    <h5 class="mb-0">{{ number_format($statCurrent?->sv_amount/100, 2, '.', ',') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3 align-self-center">
                                    <img src="https://humocard.uz/favicon.ico" class=" mb-0" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2">Ҳумо</p>
                                    <h5 class="mb-0">{{ number_format($statCurrent?->humo_amount/100, 2, '.', ',') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3 align-self-center">
                                    <i class="fas fa-times-circle h1 text-warning mb-0"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2"> Отменено</p>
                                    <h5 class="mb-0"> {{ number_format($statCurrent?->cancelled_amount/100, 2, '.', ',') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">График транзакций</h4>
                    <div>
                        @php
                            /** @var $partnerStats */
                            $chartData = $partnerStats->map(function ($stat) {
                                return [
                                    'month' => \Carbon\Carbon::parse($stat->stat_month)->translatedFormat('F Y'),
                                    'total' => round($stat->total_amount/100, 2),
                                    'sv' => round($stat->sv_amount/100, 2),
                                    'humo' => round($stat->humo_amount/100, 2),
                                    'cancelled' => round($stat->cancelled_amount/100, 2),
                                ];
                            })->values();
                        @endphp


                        <div id="stats-chart" class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Клиенты</h4>
                    <table class="table nowrap w-100" id="my-datatable">
                        <thead>
                        <tr>
                            <th class="align-middle">ИД</th>
                            <th class="align-middle">Имя</th>
                            <th class="align-middle">Электронная почта</th>
                            <th class="align-middle">Телефон</th>
                            <th class="align-middle">Роли</th>
                            <th class="text-center w-25">Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <h5 class="font-size-14 mb-1">{{ $loop->iteration }}</h5>
                                </td>
                                <td>
                                    <h5 class="font-size-14 mb-1">{{ $user->name }}</h5>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    {{ $user->phone }}
                                </td>
                                <td>
                                    <div>
                                        @foreach($user->roles as $role)
                                            <a class="badge badge-soft-primary font-size-11 m-1">{{ $role->name }}</a>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-center w-25">
                                    <form action="{{ route('users.destroy',$user->id) }}" method="post">
                                        <a href="{{ route('users.edit',$user->id) }}"
                                           class="btn border-0 btn-outline-success mx-2 btn-rounded waves-effect waves-light">
                                            <i class="fas fa-user-edit font-size-16 align-middle"></i>
                                        </a>
                                        @csrf
                                        @method('delete')
                                        <button type="button"
                                                class="submitButtonConfirm btn border-0 btn-outline-danger btn-rounded waves-effect waves-light">
                                            <i class="fas fa-user-times font-size-16 align-middle"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--    canvas--}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="config"
         aria-labelledby="offcanvasRightLabel" style="visibility: hidden;"
         aria-hidden="true">
        <div class="offcanvas-header ">
            <h5 id="offcanvasRightLabel">Конфигурация для <span
                    class="fw-bold text-primary">{{ $partner->name }}</span></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Закрыть"></button>
        </div>

            <div class="offcanvas-body">
                <form action="{{ route('partners.configurations',$partner->id) }}" method="post" id="edit-partner-form">
                    @method('PUT')
                    @csrf
                    <div class="row">
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
            <div class="modal-footer text-end p-5">
                <button type="submit" form="edit-partner-form" class="btn btn-outline-success mx-2 w-md"><i
                            class="fa fa-save"></i> Сохранить
                </button>
                <button type="button" class="btn btn-outline-secondary w-md"
                        data-bs-dismiss="offcanvas"
                        aria-label="Закрыть">Закрыть
                </button>
            </div>
    </div>
@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>

    <!-- crypto-wallet init -->
    <script src="{{ URL::asset('/assets/js/pages/crypto-wallet.init.js') }}"></script>

    <script type="text/javascript">
        // Filter
        document.addEventListener('DOMContentLoaded', function () {
            const dateOperator = document.getElementById('date_operator');
            const dateInput = document.getElementById('date');
            const datePairInput = document.getElementById('date_pair');
            const datePairContainer = document.getElementById('date_pair_container');
            const resultsDiv = document.getElementById('filter-results');
            function sendAjax() {
                const data = {
                    date_operator: dateOperator.value,
                    date: dateInput.value,
                    date_pair: datePairInput.value
                };

                if (data.date_operator === 'between' && !data.date_pair) return;

                fetch("{{ route('filter.partner.stats', $partner->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                    .then(async res => {
                        if (!res.ok) {
                            const err = await res.json().catch(() => ({}));
                            const msg = err.message || 'Нет данных за выбранный период';
                            resultsDiv.innerHTML = `<div class="card-body border-top"><div class="row"><div class="text-warning text-center my-3">${msg}</div> </div></div>`;
                            return null;
                        }

                        return res.json();
                    })
                    .then(data => {
                        if (!data) return;

                        let html = '<div class="card-body border-top"><div class="row">';

                        data.forEach((item, index) => {
                            const title = item.type === 'system' ? 'Система' : 'Пользователь';
                            const amount = item.total_amount;
                            const count = item.total_count;

                            html += `
                                <div class="col-sm-6">
                                    ${index === 1 ? `<div class="text-sm-end mt-4 mt-sm-0">` : '<div>'}
                                        <p class="text-muted mb-2">${title}</p>
                                        <h5>${amount} <span class="badge badge-soft-info">${count}</span></h5>
                                    </div>
                                </div>`;
                        });

                        html += '</div></div>';
                        resultsDiv.innerHTML = html;
                    })
                    .catch(err => {
                        // console.error('AJAX Error:', err);
                        resultsDiv.innerHTML = `<div class="card-body border-top"><div class="row"><div class="text-danger text-center">Ошибка при получении данных.</div> </div></div>`;
                    });

            }


            // Toggle second date input
            dateOperator.addEventListener('change', () => {
                const isBetween = dateOperator.value === 'between';
                datePairContainer.style.display = isBetween ? 'block' : 'none';

                if (!isBetween) {
                    datePairInput.value = '';
                    sendAjax();
                }
            });

            // Trigger on value change
            dateInput.addEventListener('change', sendAjax);
            datePairInput.addEventListener('change', sendAjax);
        });


        $(document).ready(function () {
                const chartData = @json($chartData);
                chartData.reverse();
                const categories = chartData.map(row => row.month);
                const totalAmounts = chartData.map(row => row.total);
                const svAmounts = chartData.map(row => row.sv);
                const humoAmounts = chartData.map(row => row.humo);
                const cancelled = chartData.map(row => row.cancelled);


                const options = {
                series: [
                    { type: "area", name: "Общая сумма", data: totalAmounts },
                    { type: "area", name: "Отмененная сумма", data: cancelled },
                    { type: "area", name: "Uzcard", data: svAmounts },
                    { type: "area", name: "Humo", data: humoAmounts },
                ],
                chart: {
                    height: 310,
                    type: "line",
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            pan: true,
                        }
                    }
                },
                stroke: { curve: "smooth", width: 3, dashArray: [0, 0, 3, 3] },
                fill: {
                    type: "solid",
                    opacity: [0.08, 0.09, 0.1, 0.1]
                },
                legend: {
                    show: true,
                    position: 'top'
                },
                xaxis: {
                    categories: categories
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('ru-RU').format(val);
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('ru-RU', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(val) + ' сум';
                        }
                    },
                },
                colors: ["#3452e1", "#f1b44c", "#34c38f", "#50a5f1"]
            };

            const chartStats = document.querySelector("#stats-chart");
            if (chartStats) {
                const chart = new ApexCharts(chartStats, options);
                chart.render();
            }
        });


        //datatable
        $('#my-datatable').DataTable({
            pageLength: 10,
            lengthChange: false,
            responsive: true,
            language: {
                paginate: {
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>'
                },
                info: 'Показано _START_ до _END_ из _TOTAL_ записей',
                lengthMenu: 'Показать _MENU_ записей'
            },
            drawCallback: function () {
                let paginate = $('.dataTables_paginate');

                paginate.find('ul.pagination').addClass('pagination-rounded justify-content-end m-2');
                paginate.find('li').addClass('page-item');
                paginate.find('li > a, li > span').addClass('page-link');
            }
        });
    </script>
@endsection
