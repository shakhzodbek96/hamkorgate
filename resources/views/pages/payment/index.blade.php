@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Оплаты</h4>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
            <div class="row">
                <div class="col-lg-auto col-sm-12">
                    Поиск <sup class="badge badge-soft-success">{{ $payments->total() }}</sup>
                </div>
                <div class="col-lg-auto col-sm-12">
                    @if(!is_null($sum))
                        Сумма <sup class="fw-bold text-primary font-size-14">{{ nfComma($sum) }}$</sup>
                    @endif
                </div>
            </div>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-2">
                                <label >ФИО</label>
                                <input type="text" name="fio" class="form-control" value="{{ request()->fio }}">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label >Контракт</label>
                                <input type="text" name="contract" class="form-control" value="{{ request()->contract }}">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>По датам</label>
                                <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                    <input type="text" class="form-control" name="date_from" placeholder="От..">
                                    <input type="text" class="form-control" name="date_to" placeholder="До..">
                                </div>
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Тип оплаты</label>
                                <select name="type" class="form-select">
                                    <option value="">Выбрат</option>
                                    <option value="cash" @if(request()->type == 'cash') selected @endif>Наличка</option>
                                    <option value="card" @if(request()->type == 'card') selected @endif>Перевод(P2P)</option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Инвестор</label>
                                <select name="investor_id" class="form-select">
                                    <option value="">Выбрат</option>
                                    @foreach($investors as $investor)
                                        <option value="{{ $investor->id }}"
                                                @if($investor->id == request()->investor_id) selected @endif>{{ $investor->fio }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    @can("Скачать платежи в excel")
                                        <button name="export" value="1" class="btn-rounded btn btn-success">
                                            <i class="fas fa-file-excel font-size-14"></i>
                                        </button>
                                    @endcan
                                    <a href="{{ route('payments.index') }}" class="btn btn-warning">
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
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check ">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">ID</th>
                                <th class="align-middle">ФИО</th>
                                <th class="align-middle">Контракт</th>
                                <th class="align-middle">Сумма</th>
                                <th class="align-middle">Дата</th>
                                <th class="">Тип</th>
                                <th class="">Инвестор</th>
                                <th class="">Добавил(а)</th>
                                <th class="text-center">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payments->total() - ($payments->currentPage()-1)*$payments->perPage() - $loop->index }}</td>
                                    <td>
                                        <a href="{{ route('clients.show',$payment->client_id) }}">
                                            {{ $payment->client->fio ?? 'Не найден' }}</a>
                                    </td>
                                    <td>{{ $payment->client->contract ?? 'Не найден' }}</td>
                                    <td class="fw-bold {{ $payment->is_discount ? 'text-warning':'' }}"><span style="cursor: pointer" onclick="UsdToUzsShow({{ $payment->amount }},this)">{{ nfComma($payment->amount) }}$</span></td>
                                    <td>{{ $payment->date }}</td>
                                    <td>
                                        @if($payment->is_discount)
                                            <span class="font-size-12 badge badge-soft-warning">
                                                    <i class="fab fas fa-percentage"></i> Скидка
                                                </span>
                                        @else
                                            <span class="font-size-12 badge badge-soft-{{$payment->type == 'cash' ? 'success':'primary'}}">
                                                @if($payment->type == 'cash')
                                                    <i class="fab fas fa-dollar-sign"></i> Наличка
                                                @else
                                                    <i class="fas fa-credit-card"></i> Перевод карту
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('investors.show',$payment->client->investor_id ?? 0) }}">{{ $invData[$payment->client->investor_id ?? 0] ?? '-' }}</a>
                                    </td>
                                    <td>{{ $payment->user->name ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('clients.show',$payment->client_id) }}" class="btn btn-outline-success btn-rounded btn-sm"><i
                                                class="fa fa-eye"></i> Кабинет</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $payments->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
