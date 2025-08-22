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
                    @if(\App\Services\Helpers\Check::isAdmin())
                        <a href="{{ route('contract-stats.index-group-by',request()->all()) }}"
                           class="btn btn-outline-info waves-effect waves-light me-1">
                            <i class="fa fa-chart-bar align-middle font-size-16"></i> По партнеру
                        </a>
                    @endif
                    @can('Запуск платного поиска по UZCARD картам')
                        <a href="{{ route('contract-stats.search.cards',array_filter(array_merge(request()->all(),['type' => 'uzcard']), fn($i) =>  strlen($i) > 0)) }}"
                           class="btn btn-outline-primary waves-effect waves-light me-1"
                           onclick="return confirm('Вы уверены, что хотите запустить платный поиск по UZCARD картам?');">
                            <i class="fa fa-search-dollar align-middle font-size-16"></i> Запуск платного поиска по
                            UZCARD картам
                        </a>
                    @endcan
                    @can('Запуск платного поиска по HUMO картам')
                        <a href="{{ route('contract-stats.search.cards',array_filter(array_merge(request()->all(),['type' => 'humo']), fn($i) =>  strlen($i))) }}"
                           onclick="return confirm('Вы уверены, что хотите запустить платный поиск по HUMO картам?');"
                           class="btn btn-outline-warning waves-effect waves-light ">
                            <i class="fa fa-search-dollar align-middle font-size-16"></i> Запуск платного поиска по HUMO
                            картам</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card p-0">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseOne" aria-expanded="true"
                                    aria-controls="flush-collapseOne">
                                Поиск <sup class="badge badge-soft-primary"
                                           style="margin-right: 15px">{{ number_format($contracts->total()) }}</sup>
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse show"
                             aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body text-muted">
                                <form class="justify-content-end">
                                    <form>
                                        <div class="row g-3">
                                            @if(auth()->user()->is_admin)
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <label class="form-label">Выберите партнера</label>
                                                    <select class="form-select select-search-partner" name="partner_id">
                                                        @if(request()->partner_id)
                                                            <option value="{{ request()->partner_id }}" selected>
                                                                {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label">ПИНФЛ</label>
                                                <input type="text" name="pinfl" class="form-control" maxlength="14"
                                                       value="{{ request()->pinfl }}">
                                            </div>

                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label">Мерчант</label>
                                                <select class="form-select select-search" name="merchant_id">
                                                    @if(request()->merchant_id)
                                                        <option value="{{ request()->merchant_id }}" selected>
                                                            {{ \App\Models\Merchant::find(request()->merchant_id)->name }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label">Кол-карты</label>
                                                <select name="cards_count" class="form-select">
                                                    <option value="">Все</option>
                                                    <option value="1" @if(request()->cards_count == '1') selected @endif>
                                                        Без карты (Узкард)
                                                    </option>
                                                    <option value="2" @if(request()->cards_count == '2') selected @endif>
                                                        Без карты (Хумо)
                                                    </option>
                                                    <option value="0" @if(request()->cards_count === '0') selected @endif>
                                                        Без единой карты
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label">Кол-запросов</label>
                                                <select name="request_count" class="form-select">
                                                    <option value="">Все</option>
                                                    <option value="1" @if(request()->request_count == '1') selected @endif>
                                                        Без поиска (Узкард)
                                                    </option>
                                                    <option value="2" @if(request()->request_count == '2') selected @endif>
                                                        Без поиска (Хумо)
                                                    </option>
                                                    <option value="0" @if(request()->request_count === '0') selected @endif>
                                                        Без единого поиска
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label">ПЦ</label>
                                                <select class="form-select" name="type">
                                                    <option value="">Все</option>
                                                    <option value="uzcard" {{ request()->type == 'uzcard' ? 'selected':'' }}>SV</option>
                                                    <option value="humo" {{ request()->type == 'humo' ? 'selected':'' }}>HUMO</option>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-12 col-lg-6">
                                                <label class="form-label">Дата</label>
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-4 col-sm-2">
                                                        <select class="form-select" name="date_operator" onchange="
                                                            if(this.value === 'between')
                                                                document.getElementById('date_pair').style.display = 'block';
                                                            else
                                                                document.getElementById('date_pair').style.display = 'none';">
                                                            <option value="=" {{ request()->date_operator == '=' ? 'selected':'' }}>=</option>
                                                            <option value=">" {{ request()->date_operator == '>' ? 'selected':'' }}>&gt;</option>
                                                            <option value="<" {{ request()->date_operator == '<' ? 'selected':'' }}>&lt;</option>
                                                            <option value="between" {{ request()->date_operator == 'between' ? 'selected':'' }}>&#8596;</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8 col-sm-5">
                                                        <input class="form-control" type="date" name="date" value="{{ old('date',request()->date??'') }}">
                                                    </div>
                                                    <div class="col-12 col-sm-5" id="date_pair"
                                                         style="display: {{ request()->date_operator == 'between' ? 'block':'none'}}">
                                                        <input class="form-control" type="date" name="date_pair" value="{{ old('date_pair',request()->date_pair??'') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="partner_id_operator" value="=">
                                            <input type="hidden" name="merchant_id_operator" value="=">

                                            <div class="col-12 d-flex justify-content-end mt-3">
                                                <div class="btn-group w-lg w-sm" role="group">
                                                    <button type="submit" class="btn btn-primary btn-rounded">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    <a href="{{ route('contract-stats.download',request()->all()) }}" class="btn btn-success">
                                                        <i class="fa fa-file-excel"></i>
                                                    </a>
                                                    <a href="{{ route('contract-stats.index') }}" class="btn btn-warning btn-rounded">
                                                        <i class="fas fa-sync"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <x-alert-success/>
                    <div class="table-responsive">
                        <table class="table align-middle table-check myDt">
                            <thead class="table-light">
                            <tr>
                                @if(auth()->user()->is_admin)
                                    <th class="align-middle">Партнер</th>
                                @endif
                                <th class="align-middle">ПИНФЛ</th>
                                <th class="align-middle">Мерчант</th>
                                <th class="align-middle">Кол-карты</th>
                                <th class="align-middle">Кол-запросов</th>
                                <th class="align-middle">Последнее обновление</th>
                                <th class="text-center">Дата обновлено</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    @if(auth()->user()->is_admin)
                                        <td class="fw-muted">
                                            {{ $contract->partner_name}}
                                        </td>
                                    @endif
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            @can('Просмотр контракта')
                                                <a href="{{ route('contracts.show',$contract->contract_id) }}">{{ $contract->pinfl }}</a>
                                            @else
                                                {{ $contract->pinfl }}
                                            @endcan
                                        </h6>
                                    </td>
                                    <td>
                                        {{ $contract->merchant_name }}
                                    </td>
                                    <td>
                                            <span
                                                class="text-info">uzcard: </span><strong>{{ $contract->sv_cards }}</strong><br>
                                        <span
                                            class="text-warning">humo: </span><strong>{{ $contract->humo_cards }}</strong>
                                    </td>
                                    <td>
                                            <span
                                                class="text-info">uzcard: </span><strong>{{ $contract->sv_requests }}</strong><br>
                                        <span
                                            class="text-warning">humo: </span><strong>{{ $contract->humo_requests }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-info">uzcard: </span><strong
                                            class="text-muted">{{ $contract->latest_sv_request }}</strong><br>
                                        <span class="text-warning">humo: </span><strong
                                            class="text-muted">{{ $contract->latest_humo_request }}</strong>
                                    </td>
                                    <td class="text-center">
                                        {{ $contract->updated_at }}
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
                                            <form action="{{ route('contracts.update',$contract->id) }}"
                                                  method="post">
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
                                                            <input type="text"
                                                                   value="{{ $contract->merchant_name ?? ''}}"
                                                                   readonly class="form-control">
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
                                                                        @if($contract->auto == true) selected @endif>
                                                                    On
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
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
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
        $(document).ready(function () {
            // Генерация OTP и отображение модального окна
            $('#otp-delete').click(function () {
                $.ajax({
                    url: '{{ route("generate.otp") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            // Отображаем OTP в модальном окне
                            $('#otpDisplay strong').text(response.otp);
                            $('#otpDeleteModal').modal('show');
                        } else {
                            alert('Ошибка при генерации OTP');
                        }
                    },
                    error: function () {
                        alert('Ошибка на сервере');
                    }
                });
            })
        });

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
                success: function (response) {
                    if (response.success) {
                        const newStatus = !currentAutoStatus;

                        // Восстанавливаем badge с обновлённым состоянием
                        $('#loading-spinner').replaceWith(`
                    <span class="badge bg-${newStatus ? 'success' : 'danger'}" style="cursor: pointer;" onclick="toggleAutoContract(this,` + contractId + `)" data-auto="${newStatus}">
                        <i class="fas fa-${newStatus ? 'check' : 'ban'}"></i>
                    </span>
                `);
                    } else {
                        alert('Не удалось изменить статус.');

                        // Восстанавливаем оригинальный badge
                        $('#loading-spinner').replaceWith(originalBadgeHtml);
                    }
                },
                error: function (xhr) {
                    alert('Произошла ошибка. Попробуйте позже.');
                    console.error(xhr.responseText);

                    // Восстанавливаем оригинальный badge
                    $('#loading-spinner').replaceWith(originalBadgeHtml);
                }
            });
        }
    </script>
@endsection
