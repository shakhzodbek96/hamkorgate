@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-sm-0 font-size-18">Граждане</h3>

                <div class="page-title-right">
                    @can('Создание клиента')
                        <a href="#" type="button"
                           data-bs-toggle="offcanvas" data-bs-target="#create_client" aria-controls="offcanvasRight"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light me-2">
                            <i class="bx bx-bookmark-plus align-middle font-size-16"></i> Создать гражданина</a>
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="create_client"
                             aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Создать гражданина</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="{{ route('citizen.store') }}" method="post">
                                    @csrf
                                    <div id="phoneDiv">
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">ПИНФЛ</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="pinfl"
                                                       class="form-control input-mask @error('pinfl') is-invalid @enderror"
                                                       data-inputmask="'mask': '99999999999999'" im-insert="true"
                                                       required value="{{ old('pinfl') }}" autocomplete="off">
                                                @error('pinfl')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Пасспорт С.Н</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-mask" name="passport"
                                                       data-inputmask="'mask': 'AA9999999'" im-insert="true" required
                                                       autocomplete="off"
                                                       oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 mb-5 pb-5">
                                        <div>
                                            <button type="button" class="btn btn-secondary w-md float-end submitButton"
                                                    data-bs-dismiss="offcanvas" aria-label="Close">
                                                Закрыт
                                            </button>
                                            <button type="submit"
                                                    class="mx-3 float-end btn btn-success waves-effect waves-light">
                                                <i class="fa fa-user"></i> Создать
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        <h2 class="card-title pb-2">
            Поиск <sup class="badge badge-soft-primary" style="margin-right: 15px">{{ $citizens->total() }}</sup>
        </h2>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row ">
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
                        @endif
                        <div class="cols-sm-12 col-lg-2 mb-3">
                            <label>ПИНФЛ</label>
                            <input type="text" name="pinfl" class="form-control"
                                   value="{{ request()->pinfl }}" maxlength="14">
                        </div>
                        <div class="cols-sm-12 col-lg-2 mb-3">
                            <label>Пасспорт С/Н</label>
                            <input type="text" name="passport" class="form-control"
                                   value="{{ request()->passport }}" maxlength="9">
                        </div>
                        <div class="cols-sm-12 col-lg-2 mb-3">
                            <label class="form-label">По месяцу</label>
                            <div class="mb-4">
                                <div class="position-relative" id="datepicker4">
                                    <input type="text" class="form-control" data-date-container='#datepicker4'
                                           data-provide="datepicker" data-date-format="MM yyyy" placeholder="Месяц Год"
                                           data-date-min-view-mode="1"
                                           name="date" autocomplete="off"
                                           value="{{ request()->date  }}">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="partner_id_operator" value="=">
                        <div class="col-sm-12 col-lg-2">
                            <div class="btn-group w-100 mt-4" role="group">
                                <button type="submit" class="btn-rounded btn btn-primary">
                                    <i class="fas fa-search font-size-14"></i>
                                </button>

                                <a href="{{ route('e-gov.index') }}" class="btn-rounded btn btn-warning">
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
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <th class="align-middle">Партнер</th>
                            @endif
                            <th class="align-middle">ПИНФЛ</th>
                            <th class="align-middle">Дата рождения</th>
                            <th class="align-middle">Статус</th>
                            <th class="align-middle">Создано в</th>
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <th class="text-center">Действие</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($citizens as $citizen)
                            <tr>
                                @if(\App\Services\Helpers\Check::isAdmin())
                                    <td class="fw-semibold">{{$citizen->partner_name ?? 'Администратор'}}</td>
                                @endif
                                <td>
                                    <a href="{{route('citizen.show', $citizen->id)}}" class="mb-0 text-nowrap">
                                        {{ $citizen->pinfl }}
                                    </a>
                                    <p class="text-muted mb-0 font-size-10">
                                        {{ $citizen->passport }}
                                    </p>
                                </td>
                                <td>
                                    {{ \App\Services\Helpers\CompareOwners::compare_birth_date($citizen->pinfl) }}
                                </td>
                                <td>
                                    @switch($citizen->status)
                                        @case('pending')
                                            <span class="badge badge-soft-warning">В ожидании</span>
                                            @break
                                        @case('success')
                                            <span class="badge badge-soft-success">Успешно</span>
                                            @break
                                        @case('error')
                                            <span class="badge badge-soft-danger">Ошибка</span>
                                            @break

                                    @endswitch
                                </td>
                                <td>
                                    <h6 class="mb-0 text-truncate">
                                        {{ $citizen->created_at->format('d.m.Y H:i') }}
                                    </h6>
                                    <p class="text-muted mb-0 font-size-10">
                                        {{ $citizen->creator_name ?? 'API' }}
                                    </p>
                                </td>
                                @if(\App\Services\Helpers\Check::isAdmin())
                                    <td class="text-center w-25">
                                        <form action="{{ route('citizen.destroy',$citizen->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            @can('Редактирование клиента')
                                                <a href="#{{ $citizen->id }}" type="button"
                                                   data-bs-toggle="offcanvas"
                                                   data-bs-target="#client_edit_{{ $citizen->id }}"
                                                   aria-controls="offcanvasRight"
                                                   class="btn border-0 btn-outline-primary mx-2 btn-rounded waves-effect waves-light btn-sm"><i
                                                        class="mdi mdi-pencil font-size-18"></i></a>
                                            @endcan
                                            @can('Удаление клиента')
                                                <button type="button"
                                                        class="btn border-0 btn-outline-danger mx-2 btn-rounded waves-effect waves-light submitButtonConfirm btn-sm">
                                                    <i class="mdi mdi-trash-can font-size-18"></i>
                                                </button>
                                            @endcanany
                                        </form>
                                    </td>
                                @endif
                            </tr>
                            @can('Просмотр E-Gov')
                                <div class="offcanvas offcanvas-end" tabindex="-1"
                                     id="client_edit_{{ $citizen->id }}"
                                     aria-labelledby="offcanvasRightLabel" aria-hidden="true"
                                     style="visibility: hidden;">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Редактирование гражданина</h5>
                                        <button type="button" class="btn-close text-reset"
                                                data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('citizen.update',$citizen->id) }}" method="post">
                                        <div class="offcanvas-body">
                                            @csrf
                                            @method('PUT')
                                            <div id="phoneDiv_{{ $citizen->id }}">
                                                <div class="row mb-4">
                                                    <label class="col-sm-4 col-form-label">ПИНФЛ</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="pinfl"
                                                               class="form-control input-mask @error('pinfl') is-invalid @enderror"
                                                               data-inputmask="'mask': '99999999999999'"
                                                               im-insert="true"
                                                               required value="{{ $citizen->pinfl }}">
                                                        @error('pinfl')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                </div>
                                                <div class="row mb-4">
                                                    <label class="col-sm-4 col-form-label">Пасспорт С.Н</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-mask"
                                                               name="passport" data-inputmask="'mask': 'AA9999999'"
                                                               im-insert="true" required
                                                               value="{{ $citizen->passport }}"
                                                               oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();">
                                                    </div>
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
                                                        <i class="fa fa-user"></i> Сохранить
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $citizens->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/form-mask.init.js') }}"></script>
    <script src="{{ asset('/assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.select-search-partner').select2({
                placeholder: 'Выберите партнера',
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
