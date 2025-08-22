@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    <style>
        pre {
            overflow: auto; /* Добавляем прокрутку для длинного кода */
            padding: 1.5rem; /* Отступы для удобного чтения */
        }
        .copy-btn {
            cursor: pointer;
        }
        .copy-btn i {
            font-size: 1rem;
        }

    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Платные запросы на карты </h4>
                <div class="btn-group-sm">
                    @can('Экспорт запросов на карты')
                        <a href="{{ route('pinfl-requests.download',request()->all())}}" class="btn btn-success btn-sm">
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
                    <div class="modal fade bs-example-modal-center" id="filterModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Фильтрация запросов</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('pinfl-requests.index') }}" method="get">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label">ПИНФЛ</label>
                                                    <input type="text" class="form-control" name="pinfl" value="{{ request()->pinfl }}" maxlength="14">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Статус</label>
                                                    <select class="form-select" name="status">
                                                        <option value="">Все</option>
                                                        <option value="processing" {{ request()->status == 'processing' ? 'selected' : '' }}>В обработке</option>
                                                        <option value="success" {{ request()->status == 'success' ? 'selected' : '' }}>Успешно</option>
                                                        <option value="failed" {{ request()->status == 'failed' ? 'selected' : '' }}>Ошибка</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @if(auth()->user()->is_admin)
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Выберите партнера</label>
                                                        <select class="form-select select-filter-partner" style="width: 100%" name="partner_id">
                                                            @if(request()->partner_id)
                                                                <option value="{{ request()->partner_id }}" selected>
                                                                {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <input type="hidden" name="partner_id_operator" value="=">
                                                </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label>Тип</label>
                                                    <select name="type" class="form-select">
                                                        <option value="">Выбрать</option>
                                                        <option value="uzcard" {{ request()->type == 'uzcard' ? 'selected' : '' }}>SV</option>
                                                        <option value="humo" {{ request()->type == 'humo' ? 'selected' : '' }}>HUMO</option>
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
                                                            <select class="form-control form-control-sm" name="created_at_operator"
                                                                    onchange="
                                                                                if(this.value == 'between'){
                                                                                document.getElementById('created_at_pair').style.display = 'block';
                                                                                } else {
                                                                                document.getElementById('created_at_pair').style.display = 'none';
                                                                                }
                                                                                ">
                                                                <option value="=" {{ request()->created_at_operator == '=' ? 'selected':'' }}> = </option>
                                                                <option value=">" {{ request()->created_at_operator == '>' ? 'selected':'' }}> > </option>
                                                                <option value="<" {{ request()->created_at_operator == '<' ? 'selected':'' }}> < </option>
                                                                <option value="between" {{ request()->created_at_operator == 'between' ? 'selected':'' }}> От .. до .. </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input class="form-control form-control-sm" type="date" name="created_at" value="{{ old('created_at',request()->created_at??'') }}">
                                                        </div>
                                                        <div class="col-4" id="created_at_pair" style="display: {{ request()->created_at_operator == 'between' ? 'block':'none'}}">
                                                            <input class="form-control form-control-sm" type="date" name="created_at_pair" value="{{ old('created_at_pair',request()->created_at_pair??'') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <div class="form-group row align-items-center">
                                                        <div class="col-2">
                                                            <h6>Кол-во карт</h6>
                                                        </div>
                                                        <div class="col-2">
                                                            <select class="form-control form-control-sm" name="cards_count_operator"
                                                                    onchange="
                                                                                if(this.value == 'between'){
                                                                                document.getElementById('cards_count_pair').style.display = 'block';
                                                                                } else {
                                                                                document.getElementById('cards_count_pair').style.display = 'none';
                                                                                }
                                                                                ">
                                                                <option value="" {{ request()->cards_count_operator == '=' ? 'selected':'' }}> = </option>
                                                                <option value=">" {{ request()->cards_count_operator == '>' ? 'selected':'' }}> > </option>
                                                                <option value="<" {{ request()->cards_count_operator == '<' ? 'selected':'' }}> < </option>
                                                                <option value="between" {{ request()->cards_count_operator == 'between' ? 'selected':'' }}> От .. до .. </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input class="form-control form-control-sm" type="number" name="cards_count" value="{{ old('cards_count',request()->cards_count??'') }}">
                                                        </div>
                                                        <div class="col-4" id="cards_count_pair" style="display: {{ request()->amount_operator == 'between' ? 'block':'none'}}">
                                                            <input class="form-control form-control-sm" type="number" name="cards_count_pair" value="{{ old('cards_count_pair',request()->cards_count_pair??'') }}">
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
                                                            <select class="form-control form-control-sm" name="amount_operator"
                                                                    onchange="
                                                                                if(this.value == 'between'){
                                                                                document.getElementById('amount_pair').style.display = 'block';
                                                                                } else {
                                                                                document.getElementById('amount_pair').style.display = 'none';
                                                                                }
                                                                                ">
                                                                <option value="" {{ request()->amount_operator == '=' ? 'selected':'' }}> = </option>
                                                                <option value=">" {{ request()->amount_operator == '>' ? 'selected':'' }}> > </option>
                                                                <option value="<" {{ request()->amount_operator == '<' ? 'selected':'' }}> < </option>
                                                                <option value="between" {{ request()->amount_operator == 'between' ? 'selected':'' }}> От .. до .. </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input class="form-control form-control-sm" type="number" name="amount" value="{{ old('amount',request()->amount??'') }}">
                                                        </div>
                                                        <div class="col-4" id="amount_pair" style="display: {{ request()->amount_operator == 'between' ? 'block':'none'}}">
                                                            <input class="form-control form-control-sm" type="number" name="amount_pair" value="{{ old('amount_pair',request()->amount_pair??'') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="btn-group float-end">
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
                    <a href="{{ route('pinfl-requests.index')}}" class="btn btn-secondary btn-sm waves-effect waves-light">
                        <i class="fas fa-sync font-size-14"></i>
                        Очистить
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск @if($total != null) <sup class="badge badge-soft-primary">{{ number_format($total) }}</sup> @endif
            </h5>
            <div class="card">
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-2">
                                <label>Пинфл</label>
                                <input type="text" name="pinfl" class="form-control" value="{{ request()->pinfl }}" maxlength="14">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Статус</label>
                                <select name="status" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="pending" {{ request()->status == 'processing' ? 'selected' : '' }}>В обработке</option>
                                    <option value="success" {{ request()->status == 'success' ? 'selected' : '' }}>Успешно</option>
                                    <option value="failed" {{ request()->status == 'failed' ? 'selected' : '' }}>Ошибка</option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Тип</label>
                                <select name="type" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="uzcard" {{ request()->type == 'uzcard' ? 'selected' : '' }}>SV</option>
                                    <option value="humo" {{ request()->type == 'humo' ? 'selected' : '' }}>HUMO</option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-2 mb-3">
                                <label class="form-label">По месяцу (для счета)</label>
                                <div class="mb-4">
                                    <div class="position-relative" id="datepicker4">
                                        <input type="text" class="form-control" data-date-container='#datepicker4'
                                               data-provide="datepicker" data-date-format="MM yyyy" placeholder="Месяц Год" data-date-min-view-mode="1"
                                               name="date" autocomplete="off"
                                               value="{{ request()->date  }}">
                                    </div>
                                </div>
                            </div>
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <div class="cols-sm-12 col-lg-2 mb-3">
                                    <label>Выберите партнера</label>
                                    <select class="form-select select-search-partner" name="partner_id">
                                        @if(request()->partner_id)
                                            <option value="{{ request()->partner_id }}" selected>
                                            {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                        @endif
                                    </select>
                                </div>
                                <input type="hidden" name="partner_id_operator" value="=">
                            @endif
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('pinfl-requests.index') }}" class="btn btn-warning btn-rounded">
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
                        <table class="table align-middle table-check myDt">
                            <thead class="table-light">
                            <tr>
                                @if(\App\Services\Helpers\Check::isAdmin()) <th>Партнер</th>@endif
                                <th>Клиент</th>
                                <th>Тип</th>
                                <th>Количество карт</th>
                                <th>Статус</th>
                                <th>Дата</th>
                                <th>Последнее проведение</th>
                                <th>Кем</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pinfl_requests as $index => $request)
                                <tr>
                                    @if(\App\Services\Helpers\Check::isAdmin()) <td>
                                        <h6 class="mb-0 text-nowrap">
                                            {{ $request->partner->name ?? '--' }}
                                        </h6>
                                    </td> @endif

                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            {{ $request->pinfl ?? '--' }}
                                        </h6>
                                        <p class="font-size-10">{{ $request->owner }}</p>
                                    </td>
                                    <td>
                                        @if($request->type == 'uzcard')
                                            <span class="badge badge-soft-info">SV</span>
                                        @elseif($request->type == 'humo')
                                            <span class="badge badge-soft-success">HUMO</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->cards_count }}</td>
                                    <td>
                                        @if($request->status == 'success')
                                            <span class="badge badge-soft-success">Успешно</span>
                                        @elseif($request->status == 'failed')
                                            <span class="badge badge-soft-danger">Ошибка</span>
                                        @elseif($request->status == 'processing')
                                            <span class="badge badge-soft-info">В обработке</span>
                                        @else
                                            <span class="badge badge-soft-warning">Нет статуса</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at }}</td>
                                    <td>{{ $request->processed_at }}</td>
                                    <td>{{ $request->creator_name ?? 'API' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $pinfl_requests->withQueryString()->links() }}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.copy-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const codeBlock = btn.parentElement.querySelector('code');
                    const textToCopy = codeBlock.innerText;

                    navigator.clipboard.writeText(textToCopy).then(() => {
                        btn.innerHTML = '<i class="fa fa-check"></i>'; // Иконка подтверждения
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fa fa-copy"></i>'; // Вернуть иконку копирования
                        }, 2000);
                    });
                });
            });
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
        $('.select-filter-partner').select2({
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
        $("#reset_form").on('click',function () {
            $('form :input').val('');
            $("form :input[class*='like-operator']").val('like');
            $( "div[id*='_pair']" ).hide();
        });
    </script>
@endsection
