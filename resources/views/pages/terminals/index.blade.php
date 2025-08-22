@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Список терминалов</h4>
                @can("Создание терминала")
                    <div class="page-title-right">
                        <a href="#" type="button"
                           data-bs-toggle="offcanvas" data-bs-target="#create_terminal" aria-controls="offcanvasRight"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light">
                            <i class="bx bx-bookmark-plus align-middle font-size-16"></i> Создать терминал</a>
                    </div>
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="create_terminal"
                         aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                        <div class="offcanvas-header">
                            <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Создать терминал</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <form action="{{ route('terminals.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-lg-6 col-sm-12">
                                        <label class="form-label">Тип терминала</label>
                                        <select name="type" class="form-select" required>
                                            <option value="">Выбрать тип</option>
{{--                                            <option value="uzcard">Uzcard</option>--}}
                                            <option value="humo" @if(old('type') == 'humo') selected @endif>Humo</option>
                                            <option value="sv" @if(old('type') == 'sv') selected @endif>Sv</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-lg-6 col-sm-12">
                                        <label class="form-label">Мерчант</label>
                                        <input type="text" name="merchant_id" class="form-control"
                                               value="{{ old('merchant_id') }}">
                                    </div>
                                    <div class="mb-3 col-lg-6 col-sm-12">
                                        <label class="form-label">Терминал</label>
                                        <input type="text" name="terminal_id" class="form-control"
                                               value="{{ old('terminal_id') }}">
                                    </div>
                                </div>
                                <div class="mt-4 mb-5 pb-5">
                                    <div>
                                        <button type="button" class="btn btn-secondary w-md float-end submitButton"
                                                data-bs-dismiss="offcanvas" aria-label="Close">
                                            Закрыть
                                        </button>
                                        <button type="submit"
                                                class="mx-3 float-end btn btn-success waves-effect waves-light">
                                            <i class="fa fa-calculator"></i> Создать
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-success">{{ $terminals->total() }}</sup>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-3">
                                <label>Мерчант</label>
                                <input type="text" name="merchant_id" class="form-control"
                                       value="{{ request()->merchant_id }}" max="20">
                            </div>
                            <div class="cols-sm-12 col-lg-3">
                                <label>Терминал</label>
                                <input type="text" name="terminal_id" class="form-control"
                                       value="{{ request()->terminal_id }}" max="9">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Тип</label>
                                <select name="type" class="form-select">
                                    <option value="">Все</option>
                                    @foreach(\App\Models\Terminal::TYPES as $type)
                                        <option value="{{ $type }}"
                                                @if($type == request()->type) selected @endif>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(auth()->user()->is_admin)
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
                                    <button type="submit" class="btn-rounded btn btn-primary">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('terminals.index') }}" class="btn-rounded btn btn-warning">
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
                                <th class="align-middle">ID</th>
                                @if(auth()->user()->is_admin)
                                    <th class="align-middle">Партнер</th>
                                @endif
                                <th class="align-middle">Тип</th>
                                <th class="align-middle">Мерчант</th>
                                <th class="align-middle">Терминал</th>
                                <th class="align-middle">Создано</th>
                                <th class="align-middle">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($terminals as $terminal)
                                <tr>
                                    <td>{{ $terminal->id }}</td>
                                    @if(auth()->user()->is_admin)
                                        <td class="fw-bold">{{ $terminal->partner->name ?? ''}}</td>
                                    @endif
                                    <td class="fw-semibold">{{ ucfirst($terminal->type) }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ array_key_exists($terminal->terminal_id,$merchants) ? 'primary':'warning' }} font-size-11">
                                            {{ $merchants[$terminal->terminal_id] ?? 'Not used' }}
                                        </span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            T: {{ $terminal->terminal_id }}
                                        </h6>
                                        <p class="text-muted mb-0 font-size-10">
                                            M: {{ $terminal->merchant_id }}
                                        </p>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            {{ $terminal->creator->name ?? 'By API' }}
                                        </h6>
                                        <p class="text-muted mb-0 font-size-10">
                                            {{ $terminal->created_at->format('d.m.Y H:i') }}
                                        </p>
                                        <p class="text-muted mb-0 font-size-10">
                                            {{ $terminal->comment }}
                                        </p>
                                    </td>
                                    <td>
                                        <div class="d-flex me-2">
                                            <form action="{{ route('terminals.destroy',$terminal->id) }}" method="post">

                                                @method('delete') @csrf
                                                @can('Редактирование терминала')
                                                    <a href=""
                                                       class="btn border-0 btn-outline-primary btn-rounded waves-effect waves-light btn-sm"
                                                       data-bs-toggle="offcanvas"
                                                       data-bs-target="#edit_terminal_{{ $terminal->id }}"
                                                       aria-controls="offcanvasRight">
                                                        <i class="mdi mdi-pencil font-size-18"></i>
                                                    </a>
                                                @endcan
                                                @can('Удаление терминала')
                                                    <button type="submit"
                                                            class="btn border-0 btn-outline-danger btn-rounded waves-effect waves-light btn-sm"
                                                            onclick="return confirm('Are you sure');">
                                                        <i class="mdi mdi-delete font-size-18"></i>
                                                    </button>
                                                @endcan
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <div class="offcanvas offcanvas-end" tabindex="-1"
                                     id="edit_terminal_{{ $terminal->id }}" aria-labelledby="offcanvasRightLabel"
                                     aria-hidden="true" style="visibility: hidden;">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Изменит запис</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <form action="{{ route('terminals.update',$terminal->id) }}" method="post">
                                            @csrf
                                            @method('put')
                                            <div class="row">
                                                <div class="mb-3 col-lg-6 col-sm-12">
                                                    <label class="form-label">Тип терминала</label>
                                                    <input type="text" readonly class="form-control"
                                                           value="{{ strtoupper($terminal->type) }}">
                                                </div>
                                                <div class="mb-3 col-lg-6 col-sm-12">
                                                    <label class="form-label">Комиссия терминала</label>
                                                    <input type="text" name="commission"
                                                           class="form-control numberFormat"
                                                           value="{{ $terminal->commission }}" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-lg-6 col-sm-12">
                                                    <label class="form-label">Мерчант</label>
                                                    <input type="text" readonly class="form-control"
                                                           value="{{ $terminal->merchant_id }}">
                                                </div>
                                                <div class="mb-3 col-lg-6 col-sm-12">
                                                    <label class="form-label">Терминал</label>
                                                    <input type="text" readonly class="form-control"
                                                           value="{{ $terminal->terminal_id }}">
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
                                                        <i class="fa fa-calculator"></i> Создать
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $terminals->withQueryString()->links() }}

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
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
    </script>
@endsection
