@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Карты</h4>
                <div class="btn-group-sm">
                    @if(\App\Services\Helpers\Check::isAdmin())
                        <a href="#" type="button"
                           data-bs-toggle="offcanvas" data-bs-target="#importCards" aria-controls="offcanvasRight"
                           class="btn btn-outline-success btn-rounded waves-effect waves-light">
                            <i class="fa fa-file-excel align-middle font-size-16"></i> Импортировать карты</a>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="importCards"
                             aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Импортировать карты</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <h5 class="font-size-15">Информация:</h5>
                                <p class="text-muted mb-4">
                                    Выберите файл с картами, которые вы хотите импортировать
                                    <br>
                                    При импорте карты, вы можете указать PINFL клиента, которому принадлежит карта
                                </p>
                                <form action="{{ route('cards.import') }}" method="post"
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
                                        <div>
                                            <button type="submit" class="btn btn-success w-md col-sm-12"><i
                                                    class="fa fa-file-excel"></i> Загрузить
                                            </button>
                                        </div>
                                        <div>
                                            <h5 class="font-size-15 mt-4">Образец:</h5>
                                            <a class="btn btn-primary w-md col-sm-12" href="{{route('cards.example.download')}}">
                                                <i class="fa fa-file-download"></i>
                                                Скачать</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-primary" style="margin-right: 15px">{{ number_format($cards->total()) }}</sup>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row justify-content-around">
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
                            @endif

                                <div class="cols-sm-12 col-lg-2 mb-3">
                                    <label>ПИНФЛ</label>
                                    <input type="text" name="pinfl" class="form-control"
                                           value="{{ request()->pinfl }}" maxlength="14">
                                </div>

                            <div class="cols-sm-12 col-lg-2 mb-3">
                                <label>Маска карты</label>
                                <input type="text" name="pan" class="form-control"
                                       value="{{ request()->pan }}" maxlength="16">
                            </div>
                            <div class="cols-sm-12 col-lg-1 mb-3">
                                <label>Проверка</label>
                                <select class="form-select" name="is_verified">
                                    <option value="">Выбрать</option>
                                    <option value="true" {{ request()->is_verified == 'true' ? 'selected':'' }}>✅</option>
                                    <option value="false" {{ request()->is_verified == 'false' ? 'selected':'' }}>🚫</option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-1 mb-3">
                                <label>Тип</label>
                                <select class="form-select" name="type">
                                    <option value="">Выбрать</option>
                                    <option value="sv" {{ request()->type == 'sv' ? 'selected':'' }}>SV</option>
                                    <option value="humo" {{ request()->type == 'humo' ? 'selected':'' }}>HUMO</option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-1">
                                <label>Авто</label>
                                <select name="auto" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="true" @if(request()->auto == 'true') selected @endif>Актив ✅</option>
                                    <option value="false" @if(request()->auto == 'false') selected @endif>Отключено ❌
                                    </option>
                                </select>
                            </div>
                                <input type="hidden" name="partner_id_operator" value="=">
                                <input type="hidden" name="merchant_id_operator" value="=">
                                <input type="hidden" name="auto_operator" value="=">
                                <input type="hidden" name="is_verified_operator" value="=">
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn-rounded btn btn-primary">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>

                                    <a href="{{ route('cards') }}" class="btn-rounded btn btn-warning">
                                        <i class="fas fa-sync font-size-14"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
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
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ПИНФЛ</th>
                                        <th>Маска карты</th>
                                        <th>Телефон</th>
                                        <th>Владелец</th>
                                        <th>Тип</th>
                                        <th>Проверка</th>
                                        <th>Статус</th>
                                        <th>Авто</th>
                                        <th>Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($cards as $card)
                                        <tr class="{{ $card->trashed() ? 'table-danger' : '' }}">
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>
                                                @if(auth()->user()->is_admin)
                                                    <h6 class="mb-0 text-nowrap">
                                                        {{ $card->pinfl }}
                                                    </h6>
                                                    <span class="text-primary mb-0 font-size-10">{{ $card->partner->name ?? '-' }}</span>
                                                @else
                                                    {{ $card->pinfl }}
                                                @endif
                                            </td>
                                            <td class="text-start">
                                                <h6 class="mb-0 text-nowrap">
                                                    {{ $card->pan }}
                                                </h6>
                                                @if(auth()->user()->is_admin)
                                                    <p class="text-muted mb-0 font-size-10">
                                                        {{ $card->token }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td>{{ $card->phone ?? '---' }}</td>
                                            <td>{{ $card->owner }}</td>
                                            <td>
                                                <span class="badge badge-soft-{{ $card->type == 'sv' ? 'info':'warning' }}  text-uppercase">
                                                    {{$card->type}}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if(!$card->trashed())
                                                    @can('Действия с картами')
                                                        <i class="fas fa-{{ $card->is_verified === true ? 'check' : 'ban' }} text-{{ $card->is_verified === true ? 'primary': 'danger' }}"
                                                           style="cursor: pointer;"
                                                           data-card-id="{{ $card->uuid }}"
                                                           data-is-verified="{{ $card->is_verified }}"
                                                           onclick="toggleVerified(this)">
                                                        </i>
                                                    @else
                                                        <i class="fas fa-{{ $card->is_verified === true ? 'check' : 'ban' }} text-{{ $card->is_verified === true ? 'primary': 'danger' }}"></i>
                                                    @endcan
                                                @else
                                                    <i class="fas fa-ban text-danger"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!$card->trashed())
                                                    @can('Действия с картами')
                                                        <i class="fas fa-{{ $card->is_blocked === true ? 'ban' : 'check' }} text-{{ $card->is_blocked === true ? 'danger': 'success' }}"
                                                           style="cursor: pointer;"
                                                           data-card-id="{{ $card->uuid }}"
                                                           data-is-blocked="{{ $card->is_blocked }}"
                                                           onclick="toggleBlocked(this)">
                                                        </i>
                                                    @else
                                                        <i class="fas fa-{{ $card->is_blocked === true ? 'ban' : 'check' }} text-{{ $card->is_blocked === true ? 'danger': 'success' }}"></i>
                                                    @endcan
                                                @else
                                                    <i class="fas fa-ban text-danger"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!$card->trashed())
                                                    @can('Действия с картами')
                                                        <i class="fas fa-{{ $card->auto == true ? 'check' : 'ban' }} text-{{ $card->auto == true ? 'success': 'danger' }}"
                                                           style="cursor: pointer;"
                                                           data-card-id="{{ $card->uuid }}"
                                                           data-auto="{{ $card->auto }}"
                                                           onclick="toggleAuto(this)">
                                                        </i>
                                                    @else
                                                        <i class="fas fa-{{ $card->auto == true ? 'check' : 'ban' }} text-{{ $card->auto == true ? 'success': 'danger' }}"></i>
                                                    @endcan
                                                @else
                                                    <i class="fas fa-ban text-danger"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group gap-1">
                                                    @if(!$card->trashed())
                                                        <form action="{{ route('cards.delete', $card->uuid) }}"
                                                              method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                disabled>
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $cards->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    @can('Действия с картами')
        <script>
            function toggleAuto(element) {
                const cardId = $(element).data('card-id');
                const currentAutoStatus = $(element).data('auto');

                // Сохраняем текущий класс и содержимое иконки
                const originalClass = $(element).attr('class');

                // Устанавливаем спиннер
                $(element).attr('class', 'fas fa-spinner fa-spin text-primary');

                $.ajax({
                    url: `/cards/${cardId}/toggle-auto`,
                    type: 'POST',
                    data: {
                        auto: !currentAutoStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            const newStatus = !currentAutoStatus;

                            // Обновляем атрибут data-auto
                            $(element).data('auto', newStatus);

                            // Устанавливаем новую иконку
                            $(element).attr('class', `fas fa-${newStatus ? 'check' : 'ban'} text-${newStatus ? 'success' : 'danger'}`);
                        } else {
                            alert('Не удалось изменить статус.');

                            // Восстанавливаем исходную иконку
                            $(element).attr('class', originalClass);
                        }
                    },
                    error: function (xhr) {
                        alert('Произошла ошибка. Попробуйте позже.');
                        console.error(xhr.responseText);

                        // Восстанавливаем исходную иконку
                        $(element).attr('class', originalClass);
                    }
                });
            }

            function toggleVerified(element) {
                const cardId = $(element).data('card-id');
                const currentVerifiedStatus = $(element).data('is-verified');

                // Сохраняем текущий класс и содержимое иконки
                const originalClass = $(element).attr('class');

                // Устанавливаем спиннер
                $(element).attr('class', 'fas fa-spinner fa-spin text-primary');

                $.ajax({
                    url: `/cards/${cardId}/toggle-verified`,
                    type: 'POST',
                    data: {
                        verified: !currentVerifiedStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            const newStatus = !currentVerifiedStatus;

                            // Обновляем атрибут data-auto
                            $(element).data('is-verified', newStatus);

                            // Устанавливаем новую иконку
                            $(element).attr('class', `fas fa-${newStatus ? 'check' : 'ban'} text-${newStatus ? 'primary' : 'danger'}`);
                        } else {
                            alert('Не удалось изменить статус.');

                            // Восстанавливаем исходную иконку
                            $(element).attr('class', originalClass);
                        }
                    },
                    error: function (xhr) {
                        alert('Произошла ошибка. Попробуйте позже.');
                        console.error(xhr.responseText);

                        // Восстанавливаем исходную иконку
                        $(element).attr('class', originalClass);
                    }
                });
            }

            function toggleBlocked(element) {
                const cardId = $(element).data('card-id');
                const currentBlockedStatus = $(element).data('is-blocked');

                // Сохраняем текущий класс и содержимое иконки
                const originalClass = $(element).attr('class');

                // Устанавливаем спиннер
                $(element).attr('class', 'fas fa-spinner fa-spin text-primary');

                console.log(!currentBlockedStatus);

                $.ajax({
                    url: `/cards/${cardId}/toggle-blocked`,
                    type: 'POST',
                    data: {
                        blocked: !currentBlockedStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            const newStatus = !currentBlockedStatus;

                            // Обновляем атрибут data-auto
                            $(element).data('is-blocked', newStatus);

                            // Устанавливаем новую иконку
                            $(element).attr('class', `fas fa-${newStatus ? 'ban' : 'check'} text-${newStatus ? 'danger' : 'success'}`);
                        } else {
                            alert('Не удалось изменить статус блокировки.');

                            // Восстанавливаем исходную иконку
                            $(element).attr('class', originalClass);
                        }
                    },
                    error: function (xhr) {
                        alert('Произошла ошибка. Попробуйте позже.');
                        console.error(xhr.responseText);

                        // Восстанавливаем исходную иконку
                        $(element).attr('class', originalClass);
                    }
                });
            }
        </script>
    @endcan
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
        $(document).ready(function () {
            $('i[id^="balance_"]').each(function () {
                const cardElement = $(this);
                const paymentElement = $(`#payment_balance_${cardElement.attr('id').split('_')[1]}`);
                const syncElement = $(`#sync_${cardElement.attr('id').split('_')[1]}`);
                const cardToken = cardElement.data('card-uuid');

                $.ajax({
                    url: `/card/get-balance/${cardToken}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        if (response.success) {
                            cardElement.replaceWith(`<span id="balance_text_${cardElement.attr('id').split('_')[1]}" class="text-dark">${response.balance || response.message}</span>`);
                            paymentElement.replaceWith(`<span id="balance_text_${cardElement.attr('id').split('_')[1]}" class="text-dark">${response.balance || response.message}</span>`);
                            syncElement.remove(); // Удаляем кнопку sync, если запрос успешен
                        } else {
                            syncElement.removeClass('d-none'); // Показываем кнопку sync, если ошибка
                            cardElement.replaceWith(`<span id="balance_text_${cardElement.attr('id').split('_')[1]}" class="text-dark"></span>`);
                        }
                    },
                    error: function (xhr) {
                        syncElement.removeClass('d-none'); // Показываем кнопку sync, если ошибка
                        cardElement.replaceWith(`<span id="balance_text_${cardElement.attr('id').split('_')[1]}" class="text-dark"></span>`);
                        console.error(xhr.responseText);
                    }
                });
            });
        });

    </script>
@endsection
