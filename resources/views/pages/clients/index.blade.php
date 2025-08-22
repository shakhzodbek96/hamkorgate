@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Клиенты</h4>

                <div class="page-title-right">
                    @can('Создание клиента')
                        <a href="#" type="button"
                           data-bs-toggle="offcanvas" data-bs-target="#create_client" aria-controls="offcanvasRight"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light me-2">
                            <i class="bx bx-bookmark-plus align-middle font-size-16"></i> Создать клиента</a>
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="create_client"
                             aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Создать клиента</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="{{ route('clients.store') }}" method="post">
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
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Имя</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="first_name" required
                                                       maxlength="100"
                                                       oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();">
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Фамилия</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="last_name" maxlength="100"
                                                       required
                                                       oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();">
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Отчество</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="middle_name"
                                                       oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();"
                                                       required maxlength="100">
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-sm-4 col-form-label">Телефон</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-mask" name="phones[]"
                                                       data-inputmask="'mask': '99-999-99-99'" im-insert="true"
                                                       minlength="12" maxlength="12">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-lg-5">
                                            <button type="button" class="btn btn-outline-primary btn-sm float-start"
                                                    onclick="addPhone()">
                                                <i class="fa fa-plus"></i> Добавить телефон
                                            </button>
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
                    @can('Импортировать клиентов')
                        <a href="#" type="button"
                           data-bs-toggle="offcanvas" data-bs-target="#import_cliens" aria-controls="offcanvasRight"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light">
                            <i class="fa fa-file-excel align-middle font-size-16"></i> Импортировать клиентов</a>
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="import_cliens"
                             aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Импортировать клиентов</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="{{ route('clients.upload') }}" method="post"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="formrow-firstname-input" class="form-label">Выберите файл, который
                                            вы хотите загрузить</label>
                                        <input type="file" class="form-control" name="file">
                                        @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary w-md"><i
                                                    class="fa fa-file-excel"></i> Отправить
                                        </button>
                                    </div>
                                    <div>
                                        <h5 class="font-size-15 mt-4">Образец:</h5>
                                        <a class="btn btn-success w-md col-sm-12" href="{{route('clients.example.download')}}">
                                            <i class="fa fa-file-download"></i>
                                            Скачать</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-primary" style="margin-right: 15px">{{ number_format($clients->total()) }}</sup>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row justify-content-around">
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
                                <label>Имя</label>
                                <input type="text" name="first_name" class="form-control"
                                       value="{{ request()->first_name }}" maxlength="100">
                            </div>
                            <div class="cols-sm-12 col-lg-2 mb-3">
                                <label>Фамилия</label>
                                <input type="text" name="last_name" class="form-control"
                                       value="{{ request()->last_name }}" maxlength="100">
                            </div>
                            <input type="hidden" name="partner_id_operator" value="=">
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn-rounded btn btn-primary">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('clients.download',request()->all()) }}"
                                       class="btn btn-success"><i class="fa fa-file-excel"></i></a>
                                    <a href="{{ route('clients.index') }}" class="btn-rounded btn btn-warning">
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
                                <th class="align-middle">ФИО</th>
                                <th class="align-middle">Контракты</th>
                                <th class="align-middle">Создано</th>
                                <th class="text-center">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    @if(\App\Services\Helpers\Check::isAdmin())
                                        <td class="fw-semibold">{{$client->partner->name ?? ''}}</td>
                                    @endif
                                    <td>
                                        <p class="text-primary mb-0 text-nowrap">
                                            {{ $client->pinfl }}
                                        </p>
                                        <p class="text-muted mb-0 font-size-10">
                                            {{ $client->passport }}
                                        </p>
                                    </td>
                                    <td>
                                    {{ $client->fio() }}
                                    </td>
                                    <td>
                                        {{ $client->contracts_count }}
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-nowrap">
                                            {{ $client->creator->name ?? ($client->created_by == 0 ? 'API':'----') }}
                                        </h6>
                                        <p class="text-muted mb-0 font-size-10">
                                            {{ $client->created_at->format('d.m.Y H:i') }}
                                        </p>
                                    </td>
                                    {{--                                <td>--}}
                                    {{--                                    <a href="tel:{{ \App\Services\Helper::phoneFormat($client->phone) }}">{{ \App\Services\Helper::phoneShowFormatting($client->phone) }}</a>--}}
                                    {{--                                </td>--}}
                                    <td class="text-center w-25">
                                        <form action="{{ route('clients.destroy',$client->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            @can('Редактирование клиента')
                                                <a href="#{{ $client->id }}" type="button"
                                                   data-bs-toggle="offcanvas"
                                                   data-bs-target="#client_edit_{{ $client->id }}"
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
                                </tr>
                                @can('Редактирование клиента')
                                    <div class="offcanvas offcanvas-end" tabindex="-1"
                                         id="client_edit_{{ $client->id }}"
                                         aria-labelledby="offcanvasRightLabel" aria-hidden="true"
                                         style="visibility: hidden;">
                                        <div class="offcanvas-header">
                                            <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Редактирование
                                                клиента</h5>
                                            <button type="button" class="btn-close text-reset"
                                                    data-bs-dismiss="offcanvas"
                                                    aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('clients.update',$client->id) }}" method="post">
                                            <div class="offcanvas-body">
                                                @csrf
                                                @method('PUT')
                                                <div id="phoneDiv_{{ $client->id }}">
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">ПИНФЛ</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="pinfl"
                                                                   class="form-control input-mask @error('pinfl') is-invalid @enderror"
                                                                   data-inputmask="'mask': '99999999999999'"
                                                                   im-insert="true"
                                                                   required value="{{ $client->pinfl }}">
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
                                                                   value="{{ $client->passport }}"
                                                                   oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Имя</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="first_name"
                                                                   required maxlength="100"
                                                                   value="{{ $client->first_name }}"
                                                                   oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Фамилия</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="last_name"
                                                                   value="{{ $client->last_name }}"
                                                                   maxlength="100" required
                                                                   oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Отчество</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="middle_name"
                                                                   oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').toUpperCase();"
                                                                   required maxlength="100"
                                                                   value="{{ $client->middle_name }}">
                                                        </div>
                                                    </div>
                                                    @foreach($client->phones as $phone)
                                                        <div class="row mb-4" id="phoneDiv_{{ $phone->id }}">
                                                            <label class="col-sm-4 col-form-label">Телефон</label>
                                                            <div class="col-sm-6">
                                                                <input type="text" class="form-control input-mask"
                                                                       name="phones[]"
                                                                       data-inputmask="'mask': '99-999-99-99'"
                                                                       im-insert="true" minlength="12" maxlength="12"
                                                                       value="{{ \App\Services\Helpers\Helper::phoneShowFormatting($phone->phone) }}">
                                                            </div>
                                                            <button type="button"
                                                                    class="btn btn-outline-danger col-sm-2"
                                                                    onclick="deletePhone({{ $client->id }},{{ $phone->id }})">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Доп-телефон</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control input-mask"
                                                                   name="phones[]"
                                                                   data-inputmask="'mask': '99-999-99-99'"
                                                                   im-insert="true">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Доп-телефон</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control input-mask"
                                                                   name="phones[]"
                                                                   data-inputmask="'mask': '99-999-99-99'"
                                                                   im-insert="true">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <label class="col-sm-4 col-form-label">Доп-телефон</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control input-mask"
                                                                   name="phones[]"
                                                                   data-inputmask="'mask': '99-999-99-99'"
                                                                   im-insert="true">
                                                        </div>
                                                    </div>
                                                </div>
                                                {{--                                                <div class="row mb-3">--}}
                                                {{--                                                    <div class="col-lg-6 offset-lg-3">--}}
                                                {{--                                                        <button type="button"--}}
                                                {{--                                                                class="btn btn-outline-primary btn-sm float-start"--}}
                                                {{--                                                                onclick="addPhone({{ $client->id }})">--}}
                                                {{--                                                            <i class="fa fa-plus"></i> Добавить телефон--}}
                                                {{--                                                        </button>--}}
                                                {{--                                                    </div>--}}
                                                {{--                                                </div>--}}
                                                <div class="mt-4 mb-5 pb-5">
                                                    <div>
                                                        <button type="button"
                                                                class="btn btn-secondary w-md float-end submitButton"
                                                                data-bs-dismiss="offcanvas" aria-label="Close">
                                                            Закрыт
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
                                @endcan
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $clients->withQueryString()->links() }}
                </div>
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
        function addPhone(client_id = null) {
            let phone = `<div class="row mb-4">
                                        <label class="col-sm-4 col-form-label">Доп-телефон</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-mask" name="phones[]" data-inputmask="'mask': '99-999-99-99'" im-insert="true">
                                        </div>
                                    </div>`;
            if (client_id) {
                $(`#phoneDiv_${client_id}`).append(phone);
            } else {
                $('#phoneDiv').append(phone);
            }
            Inputmask().mask(document.querySelectorAll("input"));
        }
        function deletePhone(client_id, phone_id) {
            if (confirm('Вы уверены что хотите удалить телефон?')) {
                $.ajax({
                    url: `client/phones/delete`,
                    type: 'DELETE',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE',
                        phone_id: phone_id,
                        client_id: client_id
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.status === 'success') {
                            $(`#phoneDiv_${phone_id}`).remove();
                            alert(response.message)
                        } else {
                            alert(response.error);
                        }
                    }
                });
            }
        }
    </script>
@endsection
