@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18"><i class="fas fa-chart-line"></i> Результат скоринга <i class="text-primary">{{ $pinfl }}</i></h4>
                <div class="page-title-right btn-group-sm">
                    <a href="{{ route('scoring-requests.index') }}"
                       class="btn btn-outline-dark btn-rounded waves-effect waves-light me-2">
                        <i class="bx bx-arrow-back align-middle font-size-16"></i>
                        Назад
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex flex-wrap">
                                @if(count($cards) > 1)
                                    <h4 class="card-title mb-4">Отчет по картам</h4>
                                @else
                                    <h4 class="card-title mb-4">Отчет по карте</h4>
                                @endif
                            </div>
                            <div class="table-responsive">
                                @php
                                    $typeNames = [
                                        '110' => 'Пополнение счета (accounts refill)',
                                        '205' => 'Покупка/платеж, отправка P2P (purchase/payment, P2P sending)',
                                        '206' => ' P2P кредит/получение (transfer credit, P2P credit)',
                                        '207' => 'Снятие наличных (cash withdrawal)',
                                        '208' => 'Пополнение счета через наличные в банкомате (replenishment via ATM cash-in)',
                                    ];
                                @endphp

                                <div class="accordion" id="cardsAccordion">
                                    @foreach($cards as $idx => $card)
                                        @php
                                            $collapseId = 'collapseCard' . $idx;
                                            $headingId = 'headingCard' . $idx;
                                            $isFirst = $idx === 0;
                                        @endphp
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="{{ $headingId }}">
                                                <button
                                                    class="accordion-button fw-medium {{ $isFirst ? '' : 'collapsed' }}"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#{{ $collapseId }}"
                                                    aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                                    aria-controls="{{ $collapseId }}">
                                                    PAN: {{ substr($card['pan'], 0, 6) . '******' . substr($card['pan'], -4) }}
                                                </button>
                                            </h2>
                                            <div id="{{ $collapseId }}"
                                                 class="accordion-collapse collapse{{ $isFirst ? ' show' : '' }}"
                                                 aria-labelledby="{{ $headingId }}"
                                                 data-bs-parent="#cardsAccordion">
                                                <div class="accordion-body">
                                                    <table class="table table-bordered table-hover text-center mb-0">
                                                        <thead class="table-dark">
                                                        <tr>
                                                            <th>Месяц</th>
                                                            @foreach($typeNames as $type => $name)
                                                                <th>{{ $name }}<br><small>({{ $type }})</small></th>
                                                            @endforeach
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($card['monthly_data'] as $month => $typeData)
                                                            <tr>
                                                                <td class="fw-bold">{{ $month }}</td>
                                                                @foreach($typeNames as $type => $name)
                                                                    @php
                                                                        $count = $typeData[$type]['count'] ?? 0;
                                                                        $amount = $typeData[$type]['amount'] ?? 0;
                                                                    @endphp
                                                                    <td>
                                                                        <div>
                                                                            {{ number_format($amount, 0, '.', ' ') }}
                                                                            <span
                                                                                class="text-secondary">({{ $count }})</span>
                                                                        </div>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
