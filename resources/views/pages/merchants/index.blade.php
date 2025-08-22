@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Мерчанты</h4>
                @can('Создание мерчанта')
                    <div class="page-title-right">
                        <a href="{{ route('merchants.create') }}" type="button"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light">
                            <i class="bx bx-bookmark-plus align-middle font-size-16"></i> Создать мерчант</a>
                    </div>
                @endcan
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-success">{{ number_format($merchants->total()) }}</sup>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row justify-content-center">
                            <div class="cols-sm-12 col-lg-2">
                                <label>Наименование</label>
                                <input type="text" name="name" class="form-control" value="{{ request()->name }}" maxlength="100">
                                <input type="hidden" name="name_operator" value="like">
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
                            @else
                                <div class="cols-sm-12 col-lg-2 text-nowrap">
                                    <label>Телефон</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ request()->phone }}" maxlength="9">
                                </div>
                            @endif

                            <div class="cols-sm-12 col-lg-2 text-nowrap">
                                <label>Тер-л Humo</label>
                                <input type="text" name="humo_terminal" class="form-control"
                                       value="{{ request()->humo_terminal }}" maxlength="9">
                            </div>
                            <div class="cols-sm-12 col-lg-2 text-nowrap">
                                <label>Тер-л Sv</label>
                                <input type="text" name="sv_terminal" class="form-control"
                                       value="{{ request()->sv_terminal }}" maxlength="9">
                            </div>
                            <div class="cols-sm-12 col-lg-1">
                                <label>Автосписание</label>
                                <select name="auto" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="true" @if(request()->auto == 'true') selected @endif>Актив ✅</option>
                                    <option value="false" @if(request()->auto == 'false') selected @endif>Отключено ❌
                                    </option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-1">
                                <label>Режим</label>
                                <select name="is_strict" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="true" @if(request()->is_strict == 'true') selected @endif>Строгий
                                    </option>
                                    <option value="false" @if(request()->is_strict == 'false') selected @endif>
                                        Нестрогий
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('merchants.index') }}" class="btn btn-warning btn-rounded">
                                        <i class="fas fa-sync font-size-14"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="partner_id_operator" value="=">
                        <input type="hidden" name="auto_operator" value="=">
                        <input type="hidden" name="is_strict_operator" value="=">
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
                                <th class="align-middle">ID#</th>
                                <th class="align-middle">Наименование</th>
                                <th class="align-middle">Телефон</th>
                                <th class="align-middle">Терминал Humo</th>
                                <th class="align-middle">Терминал SV</th>
                                <th class="align-middle">Комиссия</th>
                                <th class="align-middle text-center">Автосписание</th>    <!-- auto -->
                                <th class="align-middle">Режим</th>
                                <th class="align-middle">Создано</th>
                                <th class="align-middle">Действие</th>          <!-- Action -->
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($merchants as $merchant)
                                <tr>
                                    <td>{{ $merchant->id }}</td>
                                    <td>
                                        @if(auth()->user()->is_admin)
                                            <h6 class="mb-0 text-nowrap">
                                                {{ $merchant->name }}
                                            </h6>
                                            <span class="text-primary mb-0 font-size-10">
                                                {{ $merchant->partner->name ?? '-' }}
                                            </span>
                                        @else
                                            {{ $merchant->name }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ \App\Services\Helpers\Helper::phoneShowFormatting($merchant->phone) }}
                                    </td>
                                    {{--                                    <td>--}}
                                    {{--                                        <h6 class="mb-0 text-nowrap">--}}
                                    {{--                                            T: {{ $merchant->uzcard_terminal }}--}}
                                    {{--                                        </h6>--}}
                                    {{--                                        <p class="text-muted mb-0 font-size-10">--}}
                                    {{--                                            M: {{ $merchant->uzcard_merchant }}--}}
                                    {{--                                        </p>--}}
                                    {{--                                    </td>--}}
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            T: {{ $merchant->humo_terminal }}
                                        </h6>
                                        <p class="text-muted mb-0 font-size-10">
                                            M: {{ $merchant->humo_merchant }}
                                        </p>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            T: {{ $merchant->sv_terminal }}
                                        </h6>
                                        <p class="text-muted mb-0 font-size-10">
                                            M: {{ $merchant->sv_merchant }}
                                        </p>
                                    </td>
                                    @if(auth()->user()->is_admin)
                                        <td>
                                        <span class="text-dark fw-bold mb-0 font-size-12">
                                            SV {{ $merchant->sv_commission }}%
                                        </span>
                                            |
                                            <span class="text-dark fw-bold mb-0 font-size-12">
                                            Humo {{ $merchant->humo_commission }}%
                                        </span>
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        <div class="btn-group dropend">
                                            <span type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-power-off text-{{ $merchant->auto ? 'success':'danger' }} font-size-16"></i>
                                            </span>
                                            @can('Редактирование мерчанта')
                                                <div class="dropdown-menu w-sm" style="">
                                                    @if($merchant->auto)
                                                        <a class="dropdown-item"
                                                           href="{{ route('merchants.toggleAuto',['merchant_id' => $merchant->id,'auto' => false]) }}">
                                                            <i class="fas fa-power-off text-danger font-size-16"></i>
                                                            Отключить
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item"
                                                           href="{{ route('merchants.toggleAuto',['merchant_id' => $merchant->id,'auto' => true]) }}">
                                                            <i class="fas fa-power-off text-success font-size-16"></i>
                                                            Включит
                                                        </a>
                                                    @endif
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-soft-{{ $merchant->is_strict ? 'danger':'secondary' }} font-size-12">
                                            {{ $merchant->is_strict ? 'Строгий':'Нестрогий' }}
                                        </span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            {{ $merchant->creator->name ?? 'By API' }}
                                        </h6>
                                        <p class="text-muted mb-0 font-size-10">
                                            {{ $merchant->created_at->format('d.m.Y H:i') }}
                                        </p>
                                    </td>
                                    <td>
                                        <div class="d-flex me-2">
                                            <form action="{{ route('merchants.destroy',$merchant->id) }}" method="POST">
                                                @method('delete')
                                                @csrf
{{--                                                @can('Просмотр мерчантов')--}}
{{--                                                    <a href="#"--}}
{{--                                                       class="btn border-0 btn-outline-info mx-2 btn-rounded waves-effect waves-light btn-sm"><i--}}
{{--                                                            class="mdi mdi-eye font-size-18"></i></a>--}}
{{--                                                @endcan--}}
                                                @can('Редактирование мерчанта')
                                                    <a href="{{ route('merchants.edit',$merchant->id) }}"
                                                       class="btn border-0 btn-outline-primary mx-2 btn-rounded waves-effect waves-light btn-sm"><i
                                                            class="mdi mdi-pencil font-size-18"></i></a>
                                                @endcan
                                                @can('Удаление мерчанта')
                                                    <button type="submit"
                                                        class="btn border-0 btn-outline-danger mx-2 btn-rounded waves-effect waves-light btn-sm"
                                                            onclick="return confirm('Are you sure');">
                                                        <i class="mdi mdi-delete font-size-18"></i>
                                                    </button>
                                                @endcan
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-3">
                        {{ $merchants->withQueryString()->links() }}
                    </div>
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
