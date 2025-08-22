@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Транзакция</h4>
                <div class="page-title-right btn-group-sm">
                    <a href="{{ route('transaction-requests.index') }}" class="btn btn-outline-dark btn-rounded waves-effect waves-light me-2">
                        <i class="bx bx-arrow-back align-middle font-size-16"></i>
                        Назад к транзакциям
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <x-alert-success/>
                <div class="card-body">
                    <h5 class="card-title pb-3">Детали транзакции</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>EXT</th>
                            <td>{{ $finalResponse['ext'] }}</td>
                        </tr>
                        <tr>
                            <th>Тип обработки</th>
                            <td>{{ $finalResponse['processing'] }}</td>
                        </tr>
                        <tr>
                            <th>Сумма транзакции</th>
                            <td>{{ number_format($finalResponse['amount'] / 100, 2, '.') }}</td>
                        </tr>
                        <tr>
                            <th>Статус транзакции</th>
                            <td>
                                @php
                                    $statusMap = [
                                        'failed' => ['class' => 'secondary', 'text' => 'Ошибка не списан'],
                                        'success' => ['class' => 'success', 'text' => 'Успешно списан'],
                                        'cancelled'=> ['class' => 'danger', 'text' => 'Транзакция отменена'],
                                        'cancel_failed'=> ['class' => 'danger', 'text' => 'Транзакция не отменена'],
                                        'dismiss'=> ['class' => 'warning', 'text' => 'Транзакция отклонена'],
                                    ];
                                    $current = $statusMap[$finalResponse['status']] ?? ['class' => 'warning', 'text' => 'Ненайденная транзакция'];
                                @endphp

                                <span class="text-{{ $current['class'] }} fw-bold">{{ $current['text'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Дата</th>
                            <td>{{ date('Y-m-d H:i:s', strtotime($finalResponse['created_at']))}}</td>
                        </tr>
                        <tr>
                            <th>Сообщение</th>
                            <td>
                                @if(is_array($finalResponse['message']))
                                    <code>@json($finalResponse['message'], JSON_PRETTY_PRINT)</code>
                                @else
                                    {{ $finalResponse['message'] }}
                                @endif
                            </td>

                        </tr>
                    </table>

                    @if($finalResponse['status'] === 'success' && is_null($transaction))
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <form action="{{ route('transaction-requests.cancel', $finalResponse['ext']) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i>
                                    Отменить транзакцию
                                </button>
                            </form>
                            <form action="{{ route('transaction-requests.restore', $finalResponse['ext']) }}" method="post">
                                @csrf
                                <button class="btn btn-success">
                                    <i class="fas fa-undo"></i>
                                    Восстановить транзакцию
                                </button>
                            </form>
                        </div>
                    @endif
                    @if($finalResponse['status'] === 'dismiss' && is_null($transaction))
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <form action="{{ route('transaction-requests.dismiss', $finalResponse['ext']) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-ban"></i>
                                    Отклонить транзакцию
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            @if($contract)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title pb-3">Информация о контракте</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>PINFL</th>
                                <td>
                                    <a href="{{ route('contracts.show', $contract->id ?? 0) }}" class="text-primary">
                                        {{ $contract->pinfl }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Loan ID</th>
                                <td>{{ $contract->loan_id }}</td>
                            </tr>
                            <tr>
                                <th>Мерчант</th>
                                <td>{{ $contract->merchant_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Общая сумма долга:</th>
                                <td>{{ number_format($contract->total_debt/100,2,'.') }}</td>
                            </tr>
                            <tr>
                                <th>Текущий долг:</th>
                                <td class="text-danger">{{ number_format($contract->current_debt/100,2,'.') }}</td>
                            </tr>
                            <tr>
                                <th>Оплачено:</th>
                                <td class="text-success">{{ number_format($contract->paid_amount/100,2,'.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-6">
            @if(isset($transaction) && !is_null($transaction))
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title pb-3">Связанная транзакция</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>RRN</th>
                                <td>
                                    <a href="{{ route('transactions.index', ['rrn' => $transaction->rrn]) }}" class="text-primary">
                                        {{ $transaction->rrn }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>EXT</th>
                                <td>{{ $transaction->ext }}</td>
                            </tr>
                            <tr>
                                <th>Статус</th>
                                <td>{{ $transaction->status }}</td>
                            </tr>
                            <tr>
                                <th>Сумма</th>
                                <td>{{ number_format($transaction->amount / 100, 2, '.') }}</td>
                            </tr>
                            <tr>
                                <th>Дата</th>
                                <td>{{ $transaction->date }}</td>
                            </tr>
                            <tr>
                                <th>Статус</th>
                                <td>
                                    @if($transaction->status == 'cancelled')
                                        <button class="btn btn-danger btn-sm" disabled>
                                            <i class="fas fa-long-arrow-alt-left"></i>
                                            Отменено
                                        </button>
                                    @else
                                        @can('Отмена транзакции')
                                            <form action="{{ route('transactions.cancel', $transaction->ext) }}" method="post">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Вы уверены?')" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-long-arrow-alt-left"></i>
                                                    Отменить
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Транзакция не найдена</h5>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
@endsection
