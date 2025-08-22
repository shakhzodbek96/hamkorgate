@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <div class="d-flex">
                                <div class="align-self-center avatar-sm me-3">
                                    <span class="avatar-title bg-primary bg-soft rounded-circle">
                                        <i class="fa fa-user font-size-18"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-muted">
                                        <h5 class="mb-1">{{ $investor->fio }}</h5>
                                        <p class="mb-0">{{ $investor->phone }}</p>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-muted">
                                        <p class="text-muted text-truncate mb-2">Баланс</p>
                                        <h5 class="mb-0 text-{{ $investor->calc['balance'] < 0 ? "danger":"primary" }} d-inline-flex">{{ nfComma($investor->calc['balance']) }}<i class="bx bx-dollar align-middle"></i></h5>
                                    </div>
                                </div>
                                <div class="dropdown ms-2">
                                    <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="offcanvas" data-bs-target="#add-investment" aria-controls="offcanvasRight">
                                        <i class="fas fa-coins font-size-16"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="add-investment" aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                                <div class="offcanvas-header">
                                    <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Изменить счёт</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">

                                    <form action="{{ route('investors.investment',$investor->id) }}" method="post">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Сумма</label>
                                            <input type="text" name="amount" class="numberFormat form-control" value="{{ old('amount') }}" required placeholder="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Тип операции</label>
                                            <select name="is_credit" class="form-select">
                                                <option value="1" @if(old('is_credit') == '1') selected @endif class="text-success">Пополнение 🔹️</option>
                                                <option value="0" @if(old('is_credit') == '0') selected @endif>Снятие денег 🔺</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Дата операции</label>
                                            <input type="date" name="date" class="form-control" value="{{ old('date',date('Y-m-d')) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Коментария</label>
                                            <input type="text" name="comment" class="form-control" value="{{ old('comment','Изменение счета') }}">
                                        </div>
                                        <div class="mt-4 mb-5 pb-5">
                                            <div>
                                                <button type="button" class="btn btn-secondary w-md float-end submitButton" data-bs-dismiss="offcanvas" aria-label="Close">
                                                    Закрыт
                                                </button>
                                                <button type="submit" class="mx-3 float-end btn btn-success waves-effect waves-light">
                                                    <i class="fa fa-user"></i> Создать
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3 pe-1">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Пополнение</p>
                                        <h5 class="mb-0 text-success d-inline-flex">{{ nfComma($investor->calc['invested']) }}<i class="bx bx-down-arrow-alt align-middle"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Снятие</p>
                                        <h5 class="mb-0 text-warning d-inline-flex">{{ nfComma($investor->calc['took']) }}<i class="bx bx-up-arrow-alt align-middle"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Расход</p>
                                        <h5 class="mb-0 text-danger d-inline-flex">{{ nfComma($investor->calc['debit']) }}<i class="bx bx-caret-up align-middle"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Приход</p>
                                        <h5 class="mb-0 text-info d-inline-flex">{{ nfComma($investor->calc['credit']) }}<i class="bx bx-caret-down align-middle"></i></h5>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                        <i class="bx bx-copy-alt"></i>
                                    </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Конракты</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>{{ $investor->clients->count() }} <i class="mdi mdi-chevron-right ms-1 text-info"></i></h4>
                                <div class="d-flex">
                                    <span class="badge badge-soft-success font-size-12"> + {{ $investor->clients->sum('income_amount') }} $</span>
                                    <span class="ms-2 text-truncate">Ожидаемый доход </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                    <i class="bx bx-archive-in"></i>
                                </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Платежи</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>$ {{ nfComma($payments->sum('amount')) }} <i class="mdi mdi-chevron-down ms-1 text-success"></i></h4>
                                <div class="d-flex">
                                    <span class="badge badge-soft-success font-size-12">
                                        + {{ nfComma($payments->where('date','>=',date('Y-m-d',strtotime('-30 days')))->sum('amount')) }} $</span>
                                    <span class="ms-2 text-truncate">За последние 30 дней</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                    <i class="bx bx-purchase-tag-alt"></i>
                                </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Сумма долга клиентов</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>$ {{ $investor->clients->sum('current_debit') }} <i class="mdi mdi-chevron-up ms-1 text-danger"></i></h4>

                                <div class="d-flex">
                                    <span class="badge badge-soft-danger font-size-12"> {{ $graphics->where('delayed','>',15)->sum('remaining_amount') }} $</span>
                                    <span class="ms-2 text-truncate">Просрочка болше 15 дней</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Транзакции <i class="bx bxs-directions text-primary"></i></h4>

                    <ul class="nav nav-pills nav-justified" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#transactions" role="tab" aria-selected="false"><i class="bx bx-transfer font-size-16 align-middle mx-1"></i>Оборот средств</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#investment" role="tab" aria-selected="true"><i class="bx bx-wallet font-size-16 align-middle mx-1"></i>Изменение счета</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="transactions" role="tabpanel">
                            <div class="table-responsive" style="max-height: 330px;">
                                <table class="table align-middle table-nowrap">
                                    <tbody>
                                    @forelse($investor->investments->whereNotIn('type',[1,2]) as $investment)
                                        <tr>
                                            <td>
                                                <div class="font-size-22 text-{{ $investment->is_credit ? 'success':'danger' }}">
                                                    <i class="bx bx-{{ $investment->is_credit ? 'down':'up' }}-arrow-circle"></i>
                                                </div>
                                            </td>
                                            <td><h5 class="font-size-14 mb-0">{{ $investment->date }}</h5></td>

                                            <td>
                                                <h5 class="font-size-14 mb-0">${{ nfComma($investment->amount) }}</h5>
                                            </td>
                                            <td class="text-wrap">
                                                <i class="mdi mdi-comment text-{{ strlen($investment->comment) ? 'success':'secondary' }} font-size-14" style="cursor: pointer" onclick="showComment(this,{{ $investment->id }})"></i>
                                                <span id="comment-{{$investment->id}}" style="display: none">{{ $investment->comment }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <p class="mt-4 text-center text-muted">Нет данных!</p>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="investment" role="tabpanel">
                            <table class="table align-middle table-nowrap">
                                <tbody>
                                @forelse($investor->investments->whereIn('type',[1,2]) as $investment)
                                    <tr>
                                        <td>
                                            <div class="font-size-22 text-{{ $investment->is_credit ? 'success':'danger' }}">
                                                <i class="bx bx-{{ $investment->is_credit ? 'down':'up' }}-arrow-circle"></i>
                                            </div>
                                        </td>
                                        <td><h5 class="font-size-14 mb-0">{{ $investment->date }}</h5></td>
                                        <td>
                                            <h5 class="font-size-14 mb-0">${{ nfComma($investment->amount) }}</h5>
                                        </td>
                                        <td class="text-wrap">
                                            <i class="mdi mdi-comment text-{{ strlen($investment->comment) ? 'success':'secondary' }} font-size-14" style="cursor: pointer" onclick="showComment(this,{{ $investment->id }})"></i>
                                            <span id="comment-{{$investment->id}}" style="display: none">{{ $investment->comment }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <p class="mt-4 text-center text-muted">Нет данных!</p>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Клиенты</h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-check" id="clients_table">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">№</th>
                                <th class="align-middle">Сумма Рассрочки</th>
                                <th class="align-middle">Сумма инвестиции</th>
                                <th class="align-middle">Контракт</th>
                                <th class="align-middle">Долг</th>
                                <th class="align-middle">Ожидаемый доход</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($investor->clients as $client)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-info">{{ nfComma($client->loan_amount) }}$</td>
                                    <td class="fw-bold text-primary">{{ nfComma($client->getNetto()) }}$</td>
                                    <td>
                                        <a href="{{ route('clients.show',$client->id) }}">{{ $client->contract }}</a>
                                    </td>
                                    <td class="fw-bold text-danger text-center">{{ nfComma($client->current_debit) }}$</td>
                                    <td class="fw-bold text-success text-center">{{ nfComma($client->income_amount) }}$</td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="99">
                                        <p class="text-muted mt-3">Нет клиентов</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>

        function showComment(obj,id) {
            $(obj).hide()
            $("#comment-"+id).show();
        }
        $('#clients_table').addClass('w-100').DataTable({
            responsive: true,
            searching: false,
            ordering: false,
            paging: false,
            info: false,
        });
    </script>
@endsection
