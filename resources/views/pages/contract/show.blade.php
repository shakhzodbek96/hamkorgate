@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Информация о контракте</h4>
                <div class="page-title-right btn-group">
                    @can('Платная синхронизация карт Uzcard')
                        <form action="{{ route('clients.uzcardCards', $contract->pinfl) }}" method="post">
                            @csrf
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <input type="hidden" name="partner_id" value="{{ $contract->partner_id }}">
                            @endif
                            <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-sync"></i>
                                Платная синхронизация Uzcard
                            </button>
                        </form>
                    @endcan
                    @can('Платная синхронизация карт Humo')
                        <form action="{{ route('clients.humoCards', $contract->pinfl) }}" method="post">
                            @csrf
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <input type="hidden" name="partner_id" value="{{ $contract->partner_id }}">
                            @endif
                            <button type="submit" class="btn btn-sm btn-outline-success me-2">
                                <i class="fas fa-sync"></i>
                                Платная синхронизация Humo
                            </button>
                        </form>
                    @endcan
                    @can('Синхронизация карт клиента')
                        <form action="{{ route('clients.syncClientCards', $contract->id) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync"></i>
                                Синхронизация карт клиента
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
            <x-alert-success/>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-info bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary text-nowrap">{{ $contract->fio() }}</h5>
                                <p class="mb-0">Пасспорт С/Н: <span
                                        class="fw-bold mx-2">{{ $contract->passport }}</span></p>
                                <p>Пинфл: <span class="fw-bold mx-2">{{ $contract->pinfl }}</span></p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="/assets/images/crypto/features-img/img-1.png" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                @if($phones->count() > 0)
                                    @foreach($phones as $phone)
                                        <div class="col-6 pt-3">
                                            <h5 class="font-size-15">
                                                <a href="tel:{{ \App\Services\Helpers\Helper::phoneFormat($phone->phone) }}">
                                                    {{ \App\Services\Helpers\Helper::phoneShowFormatting($phone->phone) }}
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-0">Телефон</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center pt-4">
                                        <p class="text-muted">Нет номер телефона</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Информация о контракте <i
                            class="mdi mdi-file-document-edit-outline text-primary font-size-16"></i></h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0 w-100 dataTable" id="DataTables_Table_0">
                            <tbody>
                            <tr>
                                <th scope="row">ID контракта</th>
                                <td class="text-wrap text-primary">{{ $contract->loan_id }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Ext ID</th>
                                <td class="text-wrap">{{ $contract->ext }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Дата создания:</th>
                                <td>{{ $contract->created_at->format('H:i:s / d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Мерчант :</th>
                                <td>
                                    <a href="{{ route('merchants.index',['name' => $contract->merchant_name]) }}">{{ $contract->merchant_name }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Автосписание :</th>
                                <td>
                                    <span class="badge {{ $contract->auto ? 'bg-success' : 'bg-danger' }}"
                                          style="cursor: pointer;" onclick="toggleAutoContract(this)"
                                          data-auto="{{ $contract->auto }}">
                                        <i class="fas fa-{{ $contract->auto ? 'check' : 'ban' }}"></i>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Общая сумма долга:</th>
                                <td>{{ number_format($contract->total_debt/100,2,'.') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Текущий долг:</th>
                                <td class="text-danger">{{ number_format($contract->current_debt/100,2,'.') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Оплачено:</th>
                                <td class="text-success">{{ number_format($contract->paid_amount/100,2,'.') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Создал:</th>
                                <td>{{ $contract->user_name ?? 'API' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Счет:</th>
                                <td>{{ $contract->account }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Информация:</th>
                                <td>{{ $contract->info }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Действия:</th>
                                <td class="text-end">
                                    <div class="d-flex justify-content-center">
                                        @can('Редактирование контракта')
                                            <button type="button"
                                                    class="btn btn-outline-primary btn-sm w-sm"
                                                    data-bs-toggle="offcanvas"
                                                    data-bs-target="#contract_edit"
                                                    aria-controls="offcanvasRight">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('Удаление контракта')
                                            <form action="{{ route('contracts.destroy',$contract->id) }}"
                                                  method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-outline-danger btn-sm ms-2 submitButtonConfirm w-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Количество карт</p>
                                    <h6 class="mb-0">
                                        Uzcard: <span
                                            class="text-primary">{{ $contract->cards->where('type','sv')->count() }}</span>
                                        <br>
                                        Humo: <span
                                            class="text-primary">{{ $contract->cards->where('type','humo')->count() }}</span>
                                    </h6>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="bx bx-copy-alt font-size-24"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">
                                        Текущий долг
                                    </p>
                                    <h class="mb-0 text-danger">
                                        <span
                                            style="cursor:pointer;">{{ number_format($contract->current_debt/100,2,'.',' ')}}</span>
                                    </h>
                                </div>

                                <div class="flex-shrink-0 align-self-center ">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-archive-in font-size-24"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Сумма транзакции:</p>
                                    <h5 class="mb-0 text-primary">{{ number_format($contract->transactions->where('status','success')->sum('amount')/100,'2','.',' ') }}</h5>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-purchase-tag-alt font-size-24"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                                <h4 class="font-size-16 fw-bold">Транзакции</h4>
                                <div class="btn-group-sm">
                                    <a href="{{ route('contracts.statistics', $contract->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-chart-bar"></i>
                                        Статистика контракта
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive" style="max-height: 300px">
                                <table class="table align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Карта</th>
                                        <th>Владелец карты</th>
                                        <th>Сумма</th>
                                        <th>Дата</th>
                                        <th>Исполнитель</th>
                                        <th>Статус</th>
                                        <th>Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($contract->transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->card['pan'] }}</td>
                                            <td>{{ $transaction->card['owner'] }}</td>
                                            <td>{{ number_format($transaction->amount/100,2,'.') }}</td>
                                            <td>{{ $transaction->date }}</td>
                                            <td>{{ $transaction->creator_name ?? 'System' }}</td>
                                            <td>
                                               <span
                                                   class="badge badge-soft-{{ $transaction->status == 'success' ? 'success':'danger' }} ">
                                                {{ $transaction->status == 'success' ? 'Успешно' : 'Отменено' }}
                                                   @if($transaction->trashed())
                                                       <i class="fa fa-trash"></i>
                                                   @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
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
                                                            <form
                                                                action="{{ route('transactions.cancel', $transaction->ext) }}"
                                                                method="post">
                                                                @csrf
                                                                <button type="submit"
                                                                        onclick="return confirm('Вы уверены?')"
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
                                                                                {{ $transaction->creator_name ?? 'System'}}
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
                                                                                {{ $transaction->reverser_name ?? 'API'}}
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
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex align-items-center justify-content-between mb-3">
                                <h4 class="font-size-16 fw-bold">Карты</h4>
                                <div class="btn-group-sm">
                                   @if( \App\Services\Helpers\Check::isAdmin())
                                        @can('Ручное добавление карты')
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#addCardModal">
                                                <i class="fa fa-plus"></i> Добавить карту
                                            </button>
                                            <div class="modal fade" id="addCardModal" tabindex="-1"
                                                 role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                Добавить карту
                                                                <span class="badge badge-soft-warning">
                                                                    <i class="fa fa-exclamation-triangle"></i>
                                                                    только карты Humo
                                                                </span>
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('cards.add') }}" method="post">
                                                                @csrf
                                                                <div class="form-group mb-0">
                                                                    <label for="cardnumberInput">Номер карты</label>
                                                                    <input id="input-mask" class="form-control input-mask" name="card_number"
                                                                           data-inputmask="'mask': '9999 9999 9999 9999'" placeholder="____ ____ ____ ____" value="{{ old('card_number') }}">

                                                                </div>
                                                                <div class="row">
                                                                    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group mt-4 mb-3">
                                                                            <label for="expirydateInput">Срок валидности</label>
                                                                            <input id="input-date1" class="form-control input-mask" name="expire"
                                                                                   data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/yy" placeholder="mm/yy" value="{{ old('expire') }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="mb-3">
                                                                            <button type="submit" class="btn btn-primary w-100">Добавить <i class="fa fa-plus"></i></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>
                                        @endcan
                                   @endif
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Токен</th>
                                            <th>Баланс</th>
                                            <th>Телефон</th>
                                            <th>Владелец</th>
                                            <th>Проверка</th>
                                            <th>Статус</th>
                                            <th>Авто</th>
                                            <th>Действие</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($contract->cards as $card)
                                            <tr class="{{ $card->trashed() ? 'table-danger' : '' }}">
                                                <th scope="row">{{ $loop->iteration }}</th>
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
                                                <td>
                                                    @if(!$card->trashed())
                                                        <span id="balance_{{ $card->id }}" class="d-none"></span>
                                                        <i id="balance_spinner_{{ $card->id }}" class="spinner-border spinner-border-sm text-info"></i>
                                                        <i id="balance_refresh_{{ $card->id }}" class="fa fa-sync mx-1 text-primary d-none" style="cursor: pointer;" onclick="getBalance({{ $card->id }}, '{{ $card->uuid }}','{{ $card->type }}')"></i>
                                                    @else
                                                        <i class="fas fa-ban text-danger"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $card->phone ?? '---' }}</td>
                                                <td>{{ $card->owner }}</td>
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
                                                               onclick="toggleBlocked(this)" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $card->is_blocked === true ? $card->block_reason : ''}}">
                                                            </i>
                                                        @else
                                                            <i class="fas fa-{{ $card->is_blocked === true ? 'ban' : 'check' }} text-{{ $card->is_blocked === true ? 'danger': 'success' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $card->is_blocked === true ? $card->block_reason : ''}}"></i>
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
                                                            @can('Ручное списание с карты')
                                                                <button type="button" class="btn btn-sm btn-primary"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#makePayment{{ $card->uuid }}">
                                                                    <i class="fa fa-credit-card"></i>
                                                                </button>
                                                                <input type="hidden" id="balance_input_{{ $card->id }}" name="balance_{{ $card->id }}">
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-primary" disabled>
                                                                    <i class="fa fa-credit-card"></i>
                                                                </button>
                                                            @endcan
                                                            @can('Удаление карты')
                                                                <form action="{{ route('cards.delete', $card->uuid) }}"
                                                                      method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-danger" disabled>
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endcan
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-danger" disabled>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @can('Ручное списание с карты')
                                        @foreach($contract->cards as $card)
                                            <div class="modal fade" id="makePayment{{ $card->uuid }}" tabindex="-1"
                                                 role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Оплата с карты {{ $card->pan }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('cards.makePayment', $card->uuid) }}"
                                                                  method="post">
                                                                @csrf
                                                                <input type="hidden" name="contract_id"
                                                                       value="{{ $contract->id }}">
                                                                <div class="form-group mb-3">
                                                                    <label for="amount">Баланс: <span
                                                                            id="payment_balance_{{ $card->id }}"></span></label>
                                                                    <input type="hidden" name="balance" id="balance_input_{{ $card->id }}">
                                                                    <input type="text" name="amount" id="amount"
                                                                           class="form-control numberFormat" required maxlength="20">
                                                                </div>
                                                                <div class="form-group">
                                                                    <button type="submit"
                                                                            class="btn btn-primary">Оплатить
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>
                                        @endforeach
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @can('Редактирование контракта')
        <div class="offcanvas offcanvas-end" tabindex="-1"
             id="contract_edit"
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
                <form action="{{ route('contracts.update',$contract->id) }}" method="post">
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
                                <input type="text" value="{{ $contract->merchant->name ?? '' }}" readonly
                                       class="form-control">
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
                            <label class="col-sm-4 col-form-label">Счет</label>
                            <div class="col-sm-8">
                                <textarea name="account" class="form-control" cols="30"
                                          rows="1">{{ $contract->account }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-sm-4 col-form-label">Доп-инфо</label>
                            <div class="col-sm-8">
                                <textarea name="info" class="form-control" cols="30"
                                          rows="3">{{ $contract->info }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4 mb-5 pb-5">
                            <div>
                                <button type="button"
                                        class="btn btn-secondary w-md float-end submitButton"
                                        data-bs-dismiss="offcanvas" aria-label="Close">
                                    Закрыть
                                </button>
                                <button type="submit"
                                        class="mx-3 float-end btn btn-primary waves-effect waves-light">
                                    <i class="fa fa-save"></i> Сохранить
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endsection
@section('script')
    <!-- form mask -->
    <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>

    <!-- form mask init -->
    <script src="{{ URL::asset('/assets/js/pages/form-mask.init.js') }}"></script>
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
        function toggleAutoContract(element) {
            const currentAutoStatus = $(element).data('auto');
            const contractId = <?php echo e($contract->id); ?>;

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
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                success: function (response) {
                    if (response.success) {
                        const newStatus = !currentAutoStatus;

                        // Восстанавливаем badge с обновлённым состоянием
                        $('#loading-spinner').replaceWith(`
                    <span class="badge bg-${newStatus ? 'success' : 'danger'}" style="cursor: pointer;" onclick="toggleAutoContract(this)" data-auto="${newStatus}">
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

        $(document).ready(function () {
            let cardList = @json($cardList);
            cardList.forEach(function (card) {
                if (card.type == 'sv')
                    getBalance(card.id, card.uuid,card.type);
                else
                {
                    let balanceText = $('#balance_' + card.id);
                    let balanceSpinner = $('#balance_spinner_' + card.id);
                    let balanceRefresh = $(`#balance_refresh_${card.id}`);

                    balanceSpinner.addClass('d-none');
                    balanceRefresh.removeClass('d-none');
                    balanceText.replaceWith(`<span id="balance_${card.id}" class="text-dark">Показать баланс</span>`);
                }
            });
        });
        function getBalance(id, uuid,type) {
            let balanceText = $('#balance_' + id);
            let balanceSpinner = $('#balance_spinner_' + id);
            let paymentElement = $(`#payment_balance_`+id);
            let balanceElement = $(`#balance_input_${id}`);
            let balanceRefresh = $(`#balance_refresh_${id}`);
            $.ajax({
                url: `/card/get-balance/${uuid}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                beforeSend: function () {
                    balanceText.addClass('d-none');
                    balanceSpinner.removeClass('d-none');
                    balanceRefresh.addClass('d-none');
                },
                success: function (response) {
                    balanceSpinner.addClass('d-none');
                    balanceRefresh.removeClass('d-none');
                    if (response.success) {
                        balanceText.replaceWith(`<span id="balance_${id}" class="text-dark">${response.balance || response.message}</span>`);
                        paymentElement.text(response.balance || response.message);
                        balanceElement.val(response.balance || response.message);

                    } else {
                        console.log(response.message+' '+id);
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                },
                complete: function () {
                    balanceRefresh.removeClass('d-none');
                }
            });
        }

    </script>
@endsection
