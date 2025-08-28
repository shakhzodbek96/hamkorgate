@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">

        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Транзакции</h4>

                <div class="page-title-right">
                    @can('Экспорт транзакций')
                        <a href="#" class="btn btn-success btn-sm">
                            <i class="fa fa-file-excel"></i>
                            Экспорт
                        </a>
                    @endcan
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light "
                            data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                        <i class="fas fa-filter align-middle"></i>
                        Фильтр
                    </button>
                    <a href="{{ route('transfers.index')}}"
                       class="btn btn-secondary btn-sm waves-effect waves-light">
                        <i class="fas fa-sync font-size-14"></i>
                        Очистить
                    </a>
                    <div class="modal fade bs-example-modal-center" id="filterModal" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Фильтрация транзакции</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('transfers.index') }}" method="get">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label">ПИНФЛ</label>
                                                    <input type="text" class="form-control" name="pinfl"
                                                           value="{{ request()->pinfl }}" maxlength="14">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="ext">Ext ID</label>
                                                    <input type="text" class="form-control" name="ext"
                                                           value="{{ request()->ext }}" maxlength="150">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="pan">Маска карты</label>
                                                    <input type="text" class="form-control" name="pan"
                                                           value="{{ request()->pan }}" maxlength="16">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="rrn">RRN</label>
                                                    <input type="text" class="form-control" name="rrn"
                                                           value="{{ request()->rrn }}" maxlength="12">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Статус транзакции</label>
                                                    <select class="form-select" name="status">
                                                        <option value="">Все</option>
                                                        <option value="success" {{ request()->status == 'success' ? 'selected':'' }} >
                                                            Успешная
                                                        </option>
                                                        <option value="cancelled" {{ request()->status == 'cancelled' ? 'selected':'' }}>
                                                            Отмененная
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            @if( \App\Services\Helpers\Check::isAdmin())
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Выберите партнера</label>
                                                        <select class="form-select select-search-partner"
                                                                style="width: 100%" name="partner_id">
                                                            @if(request()->partner_id)
                                                                <option value="{{ request()->partner_id }}" selected>
                                                                {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Выберите мерчанта</label>
                                                    <select class="form-select select-search" style="width: 100%"
                                                            name="merchant_id">
                                                        @if(request()->merchant_id)
                                                            <option value="{{ request()->merchant_id }}" selected>
                                                            {{ \App\Models\Merchant::find(request()->merchant_id)->name }}
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <div class="form-group row align-items-center">
                                                        <div class="col-2">
                                                            <h6>Дата</h6>
                                                        </div>
                                                        <div class="col-2">
                                                            <select class="form-control form-control-sm"
                                                                    name="date_operator"
                                                                    onchange="
                                                                                if(this.value == 'between'){
                                                                                document.getElementById('date_pair').style.display = 'block';
                                                                                } else {
                                                                                document.getElementById('date_pair').style.display = 'none';
                                                                                }
                                                                                ">
                                                                <option value="=" {{ request()->date_operator == '=' ? 'selected':'' }}>
                                                                    =
                                                                </option>
                                                                <option value=">" {{ request()->date_operator == '>' ? 'selected':'' }}>
                                                                    >
                                                                </option>
                                                                <option value="<" {{ request()->date_operator == '<' ? 'selected':'' }}>
                                                                    <
                                                                </option>
                                                                <option value="between" {{ request()->date_operator == 'between' ? 'selected':'' }}>
                                                                    От .. до ..
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input class="form-control form-control-sm" type="date"
                                                                   name="date"
                                                                   value="{{ old('date',request()->date??'') }}">
                                                        </div>
                                                        <div class="col-4" id="date_pair"
                                                             style="display: {{ request()->date_operator == 'between' ? 'block':'none'}}">
                                                            <input class="form-control form-control-sm" type="date"
                                                                   name="date_pair"
                                                                   value="{{ old('date_pair',request()->date_pair??'') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <div class="form-group row align-items-center">
                                                        <div class="col-2">
                                                            <h6>Сумма</h6>
                                                        </div>
                                                        <div class="col-2">
                                                            <select class="form-control form-control-sm"
                                                                    name="amount_operator"
                                                                    onchange="
                                                                                if(this.value == 'between'){
                                                                                document.getElementById('amount_pair').style.display = 'block';
                                                                                } else {
                                                                                document.getElementById('amount_pair').style.display = 'none';
                                                                                }
                                                                                ">
                                                                <option value="" {{ request()->amount_operator == '=' ? 'selected':'' }}>
                                                                    =
                                                                </option>
                                                                <option value=">" {{ request()->amount_operator == '>' ? 'selected':'' }}>
                                                                    >
                                                                </option>
                                                                <option value="<" {{ request()->amount_operator == '<' ? 'selected':'' }}>
                                                                    <
                                                                </option>
                                                                <option value="between" {{ request()->amount_operator == 'between' ? 'selected':'' }}>
                                                                    От .. до ..
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input class="form-control form-control-sm" type="number"
                                                                   name="amount"
                                                                   value="{{ old('amount',request()->amount??'') }}">
                                                        </div>
                                                        <div class="col-4" id="amount_pair"
                                                             style="display: {{ request()->amount_operator == 'between' ? 'block':'none'}}">
                                                            <input class="form-control form-control-sm" type="number"
                                                                   name="amount_pair"
                                                                   value="{{ old('amount_pair',request()->amount_pair??'') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="partner_id_operator" value="=">
                                            <input type="hidden" name="merchant_id_operator" value="=">
                                            <div class="col-12">
                                                <div class="btn-group">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search font-size-14"></i>
                                                        Фильтрация
                                                    </button>
                                                    <a class="btn btn-secondary" id="reset_form">
                                                        <i class="fas fa-sync font-size-14"></i>
                                                        Очистить
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title pb-2">
                        Поиск <sup class="badge badge-soft-primary"
                                   style="margin-right: 15px">{{ number_format($transfers->total()) }}</sup>
                    </h5>
                </div>
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-2 mb-3">
                                <label>ПИНФЛ</label>
                                <input type="text" name="pinfl" class="form-control"
                                       value="{{ request()->pinfl }}" maxlength="14">
                            </div>
                            <div class="cols-sm-12 col-lg-2 mb-3">
                                <label>Лоан-ИД</label>
                                <input type="text" name="loan_id" class="form-control"
                                       value="{{ request()->loan_id }}" maxlength="150">
                            </div>
                            <div class="cols-sm-12 col-lg-2 mb-3">
                                <label>EXT</label>
                                <input type="text" name="ext" class="form-control"
                                       value="{{ request()->ext }}" maxlength="120">
                            </div>
                            <div class="cols-sm-12 col-lg-1 mb-3">
                                <label>Статус</label>
                                <select class="form-select" name="status">
                                    <option value="">Все</option>
                                    <option value="success" {{ request()->status == 'success' ? 'selected':'' }}>
                                        Успешно
                                    </option>
                                    <option value="cancelled" {{ request()->status == 'cancelled' ? 'selected':'' }}>
                                        Отменено
                                    </option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-1 mb-3">
                                <label>ПЦ</label>
                                <select class="form-select" name="processing">
                                    <option value="">Все</option>
                                    <option value="sv" {{ request()->processing == 'sv' ? 'selected':'' }}>SV</option>
                                    <option value="humo" {{ request()->processing == 'humo' ? 'selected':'' }}>HUMO
                                    </option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-2 mb-3">
                                <label>Дата</label>
                                <input type="date" name="date" class="form-control"
                                       value="{{ request()->date }}">
                            </div>
                            <input type="hidden" name="date_operator" value="=">
                            <input type="hidden" name="status_operator" value="=">
                            <input type="hidden" name="processing_operator" value="=">
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group mt-4" role="group">
                                    <button type="submit" class="btn-rounded btn btn-primary">
                                        <i class="fas fa-search font-size-14"></i>
                                        Поиск
                                    </button>
                                    <a href="{{ route('transfers.index') }}" class="btn-rounded btn btn-secondary">
                                        <i class="fas fa-sync font-size-14"></i>
                                        Очистить
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-alert-success/>
                    <div class="table-responsive">
                        <table class="table align-middle table-check myDt">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">Клиент</th>
                                <th class="align-middle">Номер карты</th>
                                <th class="align-middle">Владелец карты</th>
                                <th class="align-middle">Сумма</th>
                                <th class="align-middle">Дата</th>
                                <th class="align-middle">Статус</th>
                                <th class="align-middle">Тип</th>
                                <th class="align-middle">Мерчант</th>
                                <th class="text-center">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transfers as $transaction)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{ route('contracts.show', $transaction->contract_id ?? 0)}}">
                                            {{ $transaction->pinfl }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        {{ $transaction->card['pan'] ?? '-' }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $transaction->card['owner'] ?? '-' }}
                                    </td>
                                    <td class="align-middle">
                                        {{ number_format($transaction->amount/100,2,'.') }}
                                    </td>
                                    <td class="align-middle nowrap">
                                        {{ $transaction->date }}
                                    </td>
                                    <td class="align-middle">
                                        @if($transaction->status == 'success')
                                            <span class="badge badge-soft-success ">Успешно</span>
                                        @elseif($transaction->status == 'cancelled')
                                            <span class="badge badge-soft-danger ">Отменено</span>
                                        @else
                                            <span class="badge badge-soft-danger ">Ошибка</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-{{ $transaction->processing == 'sv' ? 'info':'warning' }}  text-uppercase">
                                            {{$transaction->processing}}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('merchants.index', ['merchant_id' => $transaction->merchant_id]) }}">
                                            {{ $transaction->merchant_relation->name ?? 'Неизвестный мерчант' }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button"
                                                    class="btn btn-outline-primary btn-sm waves-effect waves-light me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailModal{{ $transaction->id }}">
                                                <i class="fas fa-eye align-middle"></i>
                                            </button>
                                            @if($transaction->status == 'cancelled')
                                                <button class="btn btn-danger btn-sm" disabled>
                                                    <i class="fas fa-long-arrow-alt-left"></i>
                                                    Отменено
                                                </button>
                                            @else
                                                @can('Отмена транзакции')
                                                    <form action="{{ route('transfers.cancel', $transaction->ext) }}"
                                                          method="post">
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('Вы уверены?')"
                                                                class="btn btn-sm btn-outline-danger">
                                                            <i class="fa fa-long-arrow-alt-left"></i>
                                                            Отменить
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <div class="modal fade" id="detailModal{{ $transaction->id }}" tabindex="-1"
                                     role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Подробности</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <h5 class="font-size-14">RRN:</h5>
                                                            <p class="text-muted mb-0">{{ $transaction->rrn }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <h5 class="font-size-14">Статус:</h5>
                                                            <p class="text-muted mb-0">
                                                                @if($transaction->status == 'success')
                                                                    <span class="badge badge-soft-success ">Успешно</span>
                                                                @elseif($transaction->status == 'cancelled')
                                                                    <span class="badge badge-soft-danger ">Отменено</span>
                                                                @else
                                                                    <span class="badge badge-soft-danger ">Ошибка</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <h5 class="font-size-14">ID контракта:</h5>
                                                            <p class="text-muted mb-0">
                                                                <a href="{{ route('contracts.index', ['loan_id' => $transaction->loan_id]) }}">
                                                                    {{ $transaction->loan_id }}
                                                                </a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <h5 class="font-size-14">Исполнитель:</h5>
                                                            <p class="text-muted mb-0">
                                                                <span class="badge badge-soft-success ">
                                                                    {{ $transaction->created_by == null  ? 'System' : ($transaction->owner->name ?? '-') }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <h5 class="font-size-14">Время совершения:</h5>
                                                            <p class="text-muted mb-0">{{ $transaction->created_at }}</p>
                                                        </div>
                                                    </div>
                                                    @if($transaction->status == 'cancelled')
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <h5 class="font-size-14">Дата отмены:</h5>
                                                                <p class="text-muted mb-0">
                                                                    {{ $transaction->cancelled_at }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <h5 class="font-size-14">Кем отменено:</h5>
                                                                <p class="text-muted mb-0">
                                                                <span class="badge badge-soft-warning ">
                                                                    {{ $transaction->cancelled_by == null ? 'API':($transaction->reverser->name ?? '-')}}
                                                                </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <h5 class="font-size-14">EXT:</h5>
                                                            <p class="py-1 text-black text-center rounded-pill badge-soft-info">
                                                                <span>{{ $transaction->ext }}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $transfers->withQueryString()->links() }}
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
                },
                dropdownParent: $('#filterModal')
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
                },
                dropdownParent: $('#filterModal')
            });
        });
        //Clear form filters
        $("#reset_form").on('click',function () {
            $('form :input').val('');
            $("form :input[class*='like-operator']").val('like');
            $( "div[id*='_pair']" ).hide();
        });
    </script>
@endsection
