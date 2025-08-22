@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Информация о клиенте</h4>
                <form action="{{ route('contracts.cancel',$client->id) }}" method="post">
                    @csrf
                    @can('Посмотрет коментарии')
                        <button type="button"
                                class="btn border-0 btn-info mx-2 btn-rounded waves-effect waves-light btn-sm"
                                data-bs-toggle="offcanvas" data-bs-target="#comment" aria-controls="offcanvasScrolling">
                            <i class="mdi mdi-comment font-size-14" style="cursor: pointer"></i>
                            <sub>{{ $client->comments->count() }}</sub>
                        </button>
                    @endcan
                    @if(!$client->contract && auth()->user()->can('Создать клиенты'))
                        <a href="{{ route('contracts.create',$client->id) }}"
                           class="btn border-0 btn-success mx-2 btn-rounded waves-effect waves-light btn-sm"><i
                                class="mdi mdi-book-plus-multiple font-size-18"></i></a>
                    @endif
                    @can('Изменить клиентские данные')
                        <a href="{{ route('clients.edit',$client->id) }}"
                           class="btn border-0 btn-primary mx-2 btn-rounded waves-effect waves-light btn-sm">
                            <i class="mdi mdi-pencil font-size-18"></i>
                        </a>
                    @endcan
                    @canany(["Отменит контракт",'Удалить контракт'])
                        <button type="button"
                                class="btn border-0 btn-danger mx-2 btn-rounded waves-effect waves-light submitButtonConfirm btn-sm">
                            <i class="mdi {{ (is_null($client->contract) || $client->trashed()) ? 'mdi-trash-can':'mdi-book-cancel' }} font-size-18"></i>
                        </button>
                    @endcanany
                </form>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="comment"
         aria-labelledby="offcanvasWithBothOptionsLabel" style="visibility: hidden;" aria-hidden="true">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Комментарии <sup
                    class="badge-soft-primary badge">{{$client->comments->count()}}</sup></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="chat-conversation">
                <ul class="list-unstyled" data-simplebar style="max-height: 470px; min-height: 300px">
                    @foreach($client->comments as $comment)
                        <li class="{{ $comment->user_id == auth()->id() ? "right":'' }}">
                            <div class="conversation-list">
                                @can('Удалить комментари')
                                    <div class="dropdown">
                                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                               href="{{ route('comment.delete',$comment->id) }}"><i
                                                    class="bx bx-trash"></i> Удалить</a>
                                        </div>
                                    </div>
                                @endcan
                                <div class="ctext-wrap">
                                    <div class="conversation-name">{{ $comment->user->name ?? 'User not found' }}</div>
                                    <p>
                                        {{ $comment->comment }}
                                    </p>
                                    <i>
                                        <p class="chat-time mb-0">
                                            <i class="bx bx-time-five align-middle me-1"></i> {{ $comment->created_at }}
                                        </p>
                                        @if(!is_null($comment->remind_at))
                                            <p class="chat-time mb-0 text-{{ $comment->is_reminded == 1 ? 'primary':'warning' }}">
                                                <i class="bx bx-calendar align-middle me-1"></i> {{ $comment->remind_at }}
                                            </p>
                                        @endif
                                    </i>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            @can('Добавить коммент')
                <form action="{{ route('comment.add') }}" method="post">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="p-3 chat-input-section">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for=""><i class="fa fa-calendar"></i> Напомнить в это время</label>
                                <input class="form-control mb-3" type="datetime-local" name="remind_at">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10">
                                <div class="position-relative">
                                    <textarea name="comment" cols="30" rows="1" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-send"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endcan
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-info bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h4 class="text-primary">{{ $client->fio }}</h4>
                                <p class="mb-0">Пасспорт С/Н: <span
                                        class="fw-bold float-end">{{ $client->passport_id }}</span></p>
                                <p>Пинфл: <span class="fw-bold float-end">{{ $client->pinfl }}</span></p>
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
                            <div class="pt-4">
                                <div class="row">
                                    <div class="col-5">
                                        <h5 class="font-size-15">
                                            <a href="tel:{{ phone_formatting($client->phone) }}">
                                                {{ phone_show_formatting($client->phone) }}
                                            </a>
                                        </h5>
                                        <p class="text-muted mb-0">Телефон</p>
                                    </div>
                                    <div class="col-5">
                                       @foreach($phones as $phone)
                                        <h5 class="font-size-15">
                                            <a href="tel:{{ phone_formatting($phone) }}">
                                                {{ phone_show_formatting($phone) }}
                                            </a>
                                        </h5>
                                        @endforeach
                                        <p class="text-muted mb-0">Доп тел</p>
                                    </div>
                                    <div class="col-2">
                                        @can('Подключить телеграм бот на клиента')
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-telegram fab font-size-20 waves-effect
                                                        {{ $client->chat_id ? "text-success":(isset($client->otp) ? "text-warning":"text-secondary") }}">
                                                    </i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-start" style="">
                                                    <a class="dropdown-item"
                                                       href="{{ route('clients.otp',$client->id) }}">
                                                        <i class="fa fa-key text-primary"></i> {{ $client->chat_id ? "Сгенерировать одноразовый пароль для клиента":(isset($client->otp) ? "Обновить одноразовый пароль для клиента":"Сгенерировать одноразовый пароль для клиента") }}
                                                    </a>
                                                    @isset($client->otp)
                                                        <a href="javascript: void(0);" class="dropdown-item">
                                                            <i class="fa fa-lock text-success"></i> Код подключения:
                                                            <span
                                                                class="badge badge-soft-primary font-size-13">{{ $client->otp->otp ?? '' }}</span>
                                                        </a>
                                                    @endisset
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
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
                        <table class="table table-nowrap mb-0">
                            <tbody>
                            <tr>
                                <th scope="row">Контракт №</th>
                                <td class="fw-bold text-success">{{ $client->contract }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Дата :</th>
                                <td>{{ $client->contract_date }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Инвестор :</th>
                                <td>
                                    <a href="{{ $client->investor_id ? route('investors.show',$client->investor_id):'#' }}">{{ $client->investor->fio ?? "Без инвестора" }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Сумма инвестиции:</th>
                                <td class="text-primary">{{ $client->getNetto() }}$</td>
                            </tr>
                            <tr>
                                <th scope="row">Продукт :</th>
                                <td>{{ $client->product }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Продукт код (С/Н):</th>
                                <td class="font-monospace font-size-14">{{ $client->product_code }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Месяц рассрочки:</th>
                                <td>
                                    <span class="badge-soft-primary badge font-size-12">{{ $client->month }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Отправить СМС</th>
                                <td class="text-end">
                                    @can('Отправить СМС')
                                        <button type="button"
                                                class="btn btn-outline-warning btn-sm waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#smsModal">
                                            Отправить СМС
                                        </button>
                                    @endcan
                                    @can('Отправить писмо')
                                        <a href="{{ route('mail.create',$client->id) }}"
                                           class="btn btn-outline-danger btn-sm waves-effect waves-light mx-2">
                                            Отправить писмо
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Стоимость <i class="bx bx-dollar text-warning font-size-16"></i>
                        <a href="{{ route('clients.calc',$client->id) }}" class="float-end font-size-13"><i
                                class="fa fa-recycle"></i> Перерасчет</a>
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                            <tr>
                                <th scope="row">Сумма рассрочки</th>
                                <td class="fw-bold">{{ nfComma($client->loan_amount) }} $</td>
                            </tr>
                            <tr>
                                <th scope="row">Цена продукта</th>
                                <td>{{ nfComma($client->product_price) }} $</td>
                            </tr>
                            <tr>
                                <th scope="row">Расход :</th>
                                <td>{{ nfComma($client->consumption) }} $</td>
                            </tr>
                            <tr>
                                <th scope="row">Первоначальный платеж :</th>
                                <td class="text-primary">{{ nfComma($client->initial_payment) }} $</td>
                            </tr>
                            <tr>
                                <th scope="row">Процент :</th>
                                <td>{{ "$client->percentage%" }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Общая задолженность: <span class="text-danger fw-bold">*</span></th>
                                <td>
                                    <span style="cursor:pointer;"
                                          onclick="UsdToUzsShow({{ $client->total_debit }},this)">{{ nfComma($client->total_debit) }} $</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Текущий долг :</th>
                                <td @if($client->current_debit > 0) class="text-danger" @endif>
                                    <span style="cursor:pointer;"
                                          onclick="UsdToUzsShow({{ $client->current_debit }},this)">{{ nfComma($client->current_debit) }} $</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Сумма ожидаемого дохода:</th>
                                <td class="text-warning">{{ nfComma($client->income_amount) }} $</td>
                            </tr>
                            <tr>
                                <th scope="row">Текущый сумма дохода:</th>
                                <td class="text-success">{{ nfComma($client->real_income) }} $</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-8">
            @if(session('statusMessage'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Статус почты</strong> {{ session('statusMessage') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="row">
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Товар ({{ $client->product_code }})</p>
                                    <h5 class="mb-0">{{ "$client->product" }}</h5>
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
                                        <span style="cursor:pointer;"
                                              onclick="UsdToUzsShow({{ $client->current_debit }},this)">{{ nfComma($client->current_debit) }}$</span>
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
                                    <p class="text-muted fw-medium">Сумма рассрочки:</p>
                                    <h5 class="mb-0 text-primary">{{ nfComma($client->loan_amount) }}$</h5>
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
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-start">
                                <h4 class="card-title mb-4">График оплаты</h4>
                                @if($client->total_debit > 0 && auth()->user()->can("Принимать оплаты"))
                                    <div class="ms-auto">
                                        <div class="toolbar d-flex flex-wrap gap-2 text-end">
                                            <button type="button"
                                                    class="btn btn-outline-success btn-rounded mx-1 waves-effect waves-light"
                                                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasIncome"
                                                    aria-controls="offcanvasRight">
                                                <i class="fas fa-cart-plus align-middle"></i> Оплата
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($client->total_debit > 0 && auth()->user()->can("Принимать оплаты"))
                                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasIncome"
                                     aria-labelledby="offcanvasRightLabel" aria-hidden="true"
                                     style="visibility: hidden;">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel" class="text-success">Оплата</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <form action="{{ route('payments.make',$client->id) }}" method="post">
                                            @csrf
                                            <input type="hidden" name="client_id" value="{{ $client->id }}">
                                            <div class="mb-3">
                                                <label class="float-start form-label">Дата оплаты</label>
                                                <input type="date" name="date" class="form-control"
                                                       value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="float-start form-label">Тип оплаты</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="card">Перевод на карту</option>
                                                    <option value="cash"
                                                            @if(getPaymentType() == 'cash') selected @endif>Наличка
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="float-start form-label">Валюта</label>
                                                <select name="currency" class="form-select" required>
                                                    <option value="usd" selected>USD ($)</option>
                                                    <option value="uzs">UZS (sum)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="float-start form-label">Коммент</label>
                                                <textarea class="form-control" name="comment" cols="30" rows="3"
                                                          placeholder="Any comments for payment"
                                                          maxlength="255"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="float-start form-label">Сумма оплаты USD / UZS</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control numberFormat" name="amount"
                                                           placeholder="0 USD" required autocomplete="off"
                                                           id="inputPayment"
                                                           onkeyup="UsdToUzs('inputPayment','inputPayment_uzs')">
                                                    <label class="input-group-text"><i
                                                            class="fa fa-exchange-alt"></i></label>
                                                    <input type="text" class="form-control numberFormat"
                                                           placeholder="0 UZS" autocomplete="off" id="inputPayment_uzs"
                                                           onkeyup="UzsToUsd('inputPayment','inputPayment_uzs')">
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-5 pb-5">
                                                <div>
                                                    <button type="button" class="btn btn-secondary w-md float-end"
                                                            data-bs-dismiss="offcanvas" aria-label="Close">
                                                        Закрыт
                                                    </button>
                                                    <button type="submit"
                                                            class="mx-3 float-end btn btn-success w-md waves-effect waves-light"
                                                            id="submitPayment">
                                                        <i class="fas fa-file-invoice-dollar"></i> Подтвердить платеж
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-hover table-nowrap mb-0" id="graphics_table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Дата</th>
                                            <th>Сумма</th>
                                            <th>Оплачен</th>
                                            <th>Остаток</th>
                                            <th class="text-center">Опоздание</th>
                                            <th class="table-light">С.c (test)</th>
                                            <th class="table-light">Прибыль (test)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($client->graphic as $graphic)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $graphic->date }}</td>
                                                <td>{{ nfComma($graphic->amount) }}$</td>
                                                <td class="text-{{ $graphic->paid_amount > 0 ? "success":"" }}">{{ nfComma($graphic->paid_amount) }}
                                                    $
                                                </td>
                                                <td class="text-{{ (date('Y-m-d') >= $graphic->date && $graphic->remaining_amount) ? 'danger':'' }}">
                                                    <span style="cursor:pointer;"
                                                          onclick="UsdToUzsShow({{ $graphic->remaining_amount }},this)">{{ nfComma($graphic->remaining_amount) }}$</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-soft-{{ $graphic->delayed > 0 ? "danger":"primary" }} font-size-12">{{ $graphic->delayed }}</span>
                                                </td>
                                                <td class="table-light text-primary">{{ nfComma($graphic->cost) }}$</td>
                                                <td class="table-light text-success">{{ nfComma($graphic->income) }}$
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @if($client->total_debit && auth()->user()->can('Сделать скидку и завершит контракт'))
                                        <tfoot>
                                        <div class="pt-3 float-end">
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                                Завершить контракт
                                            </button>
                                        </div>
                                        </tfoot>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade bs-example-modal-center" tabindex="-1" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Завершить контракт</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-danger text-center">
                                Внимание! Этот клиент имеет долг в размере <span class="text-primary">{{ nfComma($client->total_debit) }} $</span>.
                                Вы уверены, что хотите завершить сделку и сделать скидку на оставшиеся <span
                                    class="text-primary">{{ nfComma($client->total_debit) }} $</span>?
                            </p>
                        </div>
                        <form action="{{ route('contracts.finish',$client->id) }}" method="post">
                            @csrf
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">
                                    Отмана
                                </button>
                                <button type="button"
                                        class="btn btn-primary waves-effect waves-light submitButtonConfirm">Завершить
                                </button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center mt-2">
                                <div class="col-4">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Обшая сумма оплаты</p>
                                        <h6 class="mb-0 fw-bold">{{ $client->payments->where('is_discount',0)->sum('amount') }}</h6>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Наличка</p>
                                        <h6 class="mb-0 fw-bold text-success">{{ $client->payments->where('is_discount',0)->where('type','cash')->sum('amount') }}</h6>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Перевод на карту</p>
                                        <h6 class="mb-0 fw-bold text-primary">{{ $client->payments->where('is_discount',0)->where('type','card')->sum('amount') }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Оплаты клиента</h4>
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap table-check" id="payments_table">
                                    <thead class="table-light">
                                    <tr>
                                        <th class="align-middle">№</th>
                                        <th class="align-middle">UID</th>
                                        <th class="align-middle">Дата</th>
                                        <th class="align-middle text-center">Сумма</th>
                                        <th class="align-middle">Коммент</th>
                                        <th class="align-middle">Тип</th>
                                        <th class="align-middle">Пользователь</th>
                                        <th class="align-middle text-center">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($client->payments as $payment)
                                        <tr class="align-middle">
                                            <td class="text-muted">{{ $loop->count - $loop->iteration + 1  }}</td>
                                            <td>#{{ $payment->id }}</td>
                                            <td>{{ $payment->date }}</td>
                                            <td class="fw-bold text-{{ $payment->is_discount ? 'warning':'success' }} text-center"
                                                style="min-width: 100px">
                                                <span style="cursor:pointer;"
                                                      onclick="UsdToUzsShow({{ $payment->amount }},this)">{{ nfComma($payment->amount) }}$</span>
                                            </td>
                                            <td title="{{ $payment->comment }}">
                                                <p onclick="showDescription(this,'{{ $payment->id }}')"
                                                   style="cursor: help">{!! substr($payment->comment,0,12) !!}...</p>
                                                <p class="text-wrap" style="max-width: 200px;" hidden
                                                   id="desription_{{ $payment->id }}">{{$payment->comment}}</p>
                                            </td>
                                            <td>
                                                @if($payment->is_discount)
                                                    <span class="font-size-12 badge badge-soft-warning">
                                                    <i class="fab fas fa-percentage"></i> Скидка
                                                </span>
                                                @else
                                                    <span
                                                        class="font-size-12 badge badge-soft-{{$payment->type == 'cash' ? 'success':'primary'}}">
                                                @if($payment->type == 'cash')
                                                            <i class="fab fas fa-dollar-sign"></i> Наличка
                                                        @else
                                                            <i class="fas fa-credit-card"></i> Перевод карту
                                                        @endif
                                            </span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->user->name ?? '-' }}</td>
                                            <td class="text-center">
                                                @can("Изменить оплату")
                                                    <button
                                                        class="btn border-0 btn-sm btn-outline-success btn-rounded waves-effect waves-light"
                                                        data-bs-toggle="offcanvas"
                                                        data-bs-target="#paymentEdit-{{ $payment->id }}"
                                                        aria-controls="offcanvasRight"
                                                        type="button">
                                                        <i class="mdi mdi-lead-pencil font-size-16 align-middle"></i>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @can("Изменить оплату")
                                            <div class="offcanvas offcanvas-end" tabindex="-1"
                                                 id="paymentEdit-{{ $payment->id }}"
                                                 aria-labelledby="offcanvasRightLabel" aria-hidden="true"
                                                 style="visibility: hidden;">
                                                <div class="offcanvas-header">
                                                    <h5 id="offcanvasRightLabel" class="text-success">Изменить
                                                        оплату</h5>
                                                    <button type="button" class="btn-close text-reset"
                                                            data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                </div>
                                                <div class="offcanvas-body">

                                                    <form action="{{ route('payments.update',$payment->id) }}"
                                                          method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-3">
                                                            <label class="form-label">Дата оплаты</label>
                                                            <input type="date" name="date" class="form-control"
                                                                   value="{{ $payment->date }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="float-start form-label">Тип оплаты</label>
                                                            <select name="type" class="form-select" required>
                                                                <option value="cash"
                                                                        @if($payment->type == 'cash') selected @endif>
                                                                    Наличка
                                                                </option>
                                                                <option value="card"
                                                                        @if($payment->type == 'card') selected @endif>
                                                                    Перевод на карту
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="float-start form-label">Валюта</label>
                                                            <select name="currency" class="form-select" required>
                                                                <option value="usd"
                                                                        @if($payment->currency == 'usd') selected @endif>
                                                                    USD ($)
                                                                </option>
                                                                <option value="uzs"
                                                                        @if($payment->currency == 'uzs') selected @endif>
                                                                    UZS (sum)
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="float-start form-label">Коммент</label>
                                                            <textarea class="form-control" name="comment" cols="30"
                                                                      rows="3" placeholder="Any comments for payment"
                                                                      maxlength="255">{{ $payment->comment }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="float-start form-label">Сумма оплаты</label>
                                                            <input type="number" step="0.01" class="form-control"
                                                                   name="amount" placeholder="0" required
                                                                   autocomplete="off" value="{{ $payment->amount }}"
                                                                   max="{{ $client->total_debit+$payment->amount }}"
                                                                   min="0">
                                                        </div>
                                                        <div class="mt-4 mb-5 pb-5">
                                                            <div>
                                                                <button type="button"
                                                                        class="btn btn-secondary w-md float-end submitButton"
                                                                        data-bs-dismiss="offcanvas" aria-label="Close">
                                                                    Закрыт
                                                                </button>
                                                                <button type="submit"
                                                                        class="mx-3 float-end btn btn-success w-md waves-effect waves-light">
                                                                    <i class="fas fa-file-invoice-dollar"></i> Сохранит
                                                                    платеж
                                                                </button>
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
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Почтовый ящик клиента</h4>
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap table-hover mb-0" id="files_table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Получатель</th>
                                        <th scope="col">Адрес</th>
                                        <th scope="col">Время создание</th>
                                        <th scope="col">Время отправки</th>
                                        <th scope="col">Статус</th>
                                        <th scope="col" class="text-center">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($client->mails))
                                        @foreach($client->mails as $mail)
                                            <tr>
                                                <td class="w-25 text-wrap">
                                                    <i class="bx bxs-file font-size-16 align-middle text-{{ $mail->getClass() }} me-2"></i>{{ $mail->receiver }}
                                                </td>
                                                <td class="text-wrap w-25">{{ $mail->address }}</td>
                                                <td>{{ date('d.m.y/H:i',strtotime($mail->created_at))}}</td>
                                                <td>{{ $mail->sent_at ? date('d.m.y/H:i',strtotime($mail->sent_at)):'-' }}</td>
                                                <td class="text-uppercase">
                                                    <span
                                                        class="font-size-11 fw-bold text-{{ $mail->getClass() }}">
                                                         <span class="font-size-11 fw-bold text-{{ $mail->getClass() }}">
                                                        @if($mail->status == 'created')
                                                                 Создано
                                                             @elseif($mail->status == 'sent')
                                                                 Готов к отправке
                                                             @elseif($mail->status == 'confirmed')
                                                                 Отправлено
                                                             @elseif($mail->status == 'error')
                                                                 Ошибка
                                                             @else
                                                                 {{ $mail->status }}
                                                             @endif
                                                    </span>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('mail.delete',$mail->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        @can('Отправить писмо')
                                                            @if($mail->status == 'created')
                                                                <a href="{{ route('mail.send',$mail->id) }}"
                                                                   class="btn border-0 btn-outline-success mx-2 btn-rounded waves-effect waves-light btn-sm">
                                                                    <i class="mdi mdi-telegram font-size-18"></i>
                                                                </a>
                                                            @elseif($mail->status == 'sent')
                                                                <a href="{{ route('mail.confirm',$mail->id) }}"
                                                                   class="btn border-0 btn-outline-warning mx-2 btn-rounded waves-effect waves-light btn-sm">
                                                                    <i class="mdi mdi-telegram font-size-18"></i>
                                                                </a>
                                                            @endif
                                                        @endcan
                                                        @if($mail->status != 'created' && $mail->status != 'sent')
                                                            <a href="{{ route('mail.check',$mail->id) }}"
                                                               class="btn border-0 btn-outline-info mx-2 btn-rounded waves-effect waves-light btn-sm">
                                                                <i class="mdi mdi-update font-size-18"></i>
                                                            </a>
                                                        @endif
                                                        <a href="/{{ $mail->document }}" download
                                                           class="btn border-0 btn-outline-primary mx-2 btn-rounded waves-effect waves-light btn-sm">
                                                            <i class="mdi mdi-download font-size-18"></i>
                                                        </a>
                                                        @can('Удалить письмо')
                                                            <button type="button"
                                                                    class="submitButtonConfirm btn border-0 btn-outline-danger mx-2 btn-rounded waves-effect waves-light submitButtonConfirm btn-sm">
                                                                <i class="mdi mdi-trash-can-outline font-size-18"></i>
                                                            </button>
                                                        @endcan
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <p class="text-muted text-center">No files</p>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Файлы клиента</h4>
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap table-hover mb-0" id="files_table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Называние</th>
                                        <th scope="col">Дата загрузки</th>
                                        <th scope="col">Размер</th>
                                        <th scope="col" class="text-center">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($client->files))
                                        @foreach($client->files as $file)
                                            <tr>
                                                <td><i class="{{ $file->getFileIcon() }}"></i>{{ $file->name }}</td>
                                                <td>{{ date('d-m-Y, H:i',strtotime($file->created_at))}}</td>
                                                <td>{{ $file->getSize() }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('file.delete',$file->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <a href="/{{ $file->path }}" download
                                                           class="btn border-0 btn-outline-primary mx-2 btn-rounded waves-effect waves-light btn-sm">
                                                            <i class="mdi mdi-download font-size-18"></i>
                                                        </a>
                                                        @can('Удалить файли')
                                                            <button type="button"
                                                                    class="submitButtonConfirm btn border-0 btn-outline-danger mx-2 btn-rounded waves-effect waves-light submitButtonConfirm btn-sm">
                                                                <i class="mdi mdi-trash-can-outline font-size-18"></i>
                                                            </button>
                                                        @endcan
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <p class="text-muted text-center">No files</p>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @can("Добавить файл")
                            <div class="card-footer">
                                <form action="{{ route('file.add') }}" method="post" enctype="multipart/form-data"
                                      class="mt-4 mt-sm-0 float-sm-end d-flex">
                                    @csrf
                                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                                    <div class="hstack gap-3">
                                        <input class="form-control me-auto" type="file" name="files[]" multiple>
                                        <button type="submit" class="btn btn-success text-nowrap">
                                            <i class="mdi mdi-download"></i> Загрузить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endcan()
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="smsModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">CMC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('sms.send',$client->id) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="phone" value="{{ phone_formatting($client->phone) }}">
                        <textarea name="message" cols="30" rows="{{substr_count($message, "\n")+3}}" class="form-control">{{$message}}</textarea>
                        <div class="form-check form-check-primary mt-3 mx-1">
                            <input class="form-check-input" type="checkbox" id="formCheckcolor1" name="toAll">
                            <label class="form-check-label" for="formCheckcolor1">
                                Отправить на все номера телефонов
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Закрыт
                        </button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">Отправить</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
@section('script')
    <script>

        $('#files_table').addClass('w-100').DataTable({
            responsive: true,
            searching: false,
            ordering: false,
            paging: false,
            info: false,
        });
        $('#payments_table').addClass('w-100').DataTable({
            responsive: true,
            searching: false,
            ordering: false,
            paging: false,
            info: false,
        });
        $('#graphics_table').addClass('w-100').DataTable({
            responsive: true,
            searching: false,
            ordering: false,
            paging: false,
            info: false,
        });

    </script>
@endsection
