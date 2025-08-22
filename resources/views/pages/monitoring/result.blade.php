@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Мониторинг карт</h4>
                <div class="page-title-right btn-group-sm">
                    <a href="{{ route('monitoring-requests.index') }}"
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
                                <h4 class="card-title mb-4">Отчёт по карте {{ old('card_number') }}</h4>
                            </div>

                            <div class="table-responsive">
                                @if($error_message)
                                    <div class="alert alert-danger">{{ $error_message }}</div>
                                @endif

                                @if(isset($data) && count($data) > 0 && $type == 'humo')
                                        <table id="custom_table" class="table align-middle table-nowrap mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th scope="col">Название</th>
                                            <th scope="col">Тип</th>
                                            <th scope="col">Сумма</th>
                                            <th scope="col">RRN</th>
                                            <th scope="col">Терминал</th>
                                            <th scope="col">Мерчант</th>
                                            <th scope="col">Дата</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data as $item)
                                            <tr>
                                                <td>{{ $item['ABVR_NAME'] }}</td>
                                                <td>
                                                    @if($item['TRAN_TYPE'] == 205)
                                                        <span class="badge badge-soft-success">Покупка/Оплата</span>
                                                    @elseif($item['TRAN_TYPE'] == 206)
                                                        <span
                                                            class="badge badge-soft-info">Перевод на карту (P2P)</span>
                                                    @elseif($item['TRAN_TYPE'] == 207)
                                                        <span class="badge badge-soft-warning">Снятие наличных</span>
                                                    @elseif($item['TRAN_TYPE'] == 208)
                                                        <span class="badge badge-soft-primary">Пополнение счёта (наличные в банкомате)</span>
                                                    @elseif($item['TRAN_TYPE'] == 225)
                                                        <span
                                                            class="badge badge-soft-danger">Возврат (отмена) покупки</span>
                                                    @else
                                                        <span class="badge badge-soft-secondary">Другое</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ number_format($item['TRAN_AMT'] / 100, 2) }}
                                                    {{ $item['TRAN_CCY'] }}
                                                </td>
                                                <td>{{ $item['REF_NUMBER'] }}</td>
                                                <td>{{ $item['TERM_ID'] ?? '---' }}</td>
                                                <td>{{ $item['MERCHANT'] ?? '---' }}</td>
                                                <td>{{ str_replace('T', ' ', $item['TRAN_DATE_TIME']) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div id="paginationContainer" class="float-end pagination"></div>
                                @elseif(isset($data) && count($data) > 0 && $type == 'uzcard')
                                    <p>Общее количество записей: {{ $data['totalElements'] }}</p>
                                    <p>Общее количество : {{ $data['numberOfElements'] }}</p>
                                    <p>Общая сумма (дебет): {{ number_format($data['totalDebit']/100, 0, ',', ' ') }}
                                        <sub>uzs</sub></p>
                                    <p>Общая сумма (кредит): {{ number_format($data['totalCredit']/100, 0, ',', ' ') }}
                                        <sub>uzs</sub></p>

                                    <table class="table align-middle table-nowrap mb-0">
                                        <thead>
                                        <tr>
                                            <th>UTRN № (RRN)</th>
                                            <th>Тип транзакции</th>
                                            <th>Номер карты</th>
                                            <th>Дата</th>
                                            <th>Сумма запроса</th>
                                            <th>Мерчант</th>
                                            <th>Терминал</th>
                                            <th>Название мерчанта</th>
                                            <th>Фактическая сумма</th>
                                            <th>Сконвертированная сумма</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $result = collect($data['content'])->sortByDesc('udate')->values()->all();
                                        @endphp
                                        @foreach($result as $transaction)
                                            <tr>
                                                <td>{{ $transaction['utrnno'] }}</td>
                                                <td>
                                                    {{$transaction['transType']}}
                                                </td>
                                                <td>{{ $transaction['hpan'] }}</td>
                                                @php
                                                    $ymd = substr($transaction['udate'], 0, 4) . '-'
                                                         . substr($transaction['udate'], 4, 2) . '-'
                                                         . substr($transaction['udate'], 6, 2);

                                                    $hms = str_pad($transaction['utime'], 6, '0', STR_PAD_LEFT);

                                                    $dateTimeString = $ymd . ' '
                                                        . substr($hms, 0, 2) . ':'
                                                        . substr($hms, 2, 2) . ':'
                                                        . substr($hms, 4, 2);

                                                    $dateTimeCarbon = \Carbon\Carbon::parse($dateTimeString);
                                                    $formattedDatetime = $dateTimeCarbon->format('d.m.Y H:i:s');
                                                @endphp
                                                <td>{{ $formattedDatetime }}</td>
                                                <td>{{ number_format($transaction['reqamt']/100, 0, ',', ' ') }} <sub>uzs</sub>
                                                </td>
                                                <td>{{ $transaction['merchant'] }}</td>
                                                <td>{{ $transaction['terminal'] }}</td>
                                                <td>{{ $transaction['merchantName'] }}</td>
                                                <td>{{ number_format($transaction['actamt']/100, 0, ',', ' ') }} <sub>uzs</sub>
                                                </td>
                                                <td>{{ number_format($transaction['conamt']/100, 0, ',', ' ') }} <sub>uzs</sub>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @if($data['totalPages'] > 0)
                                        <div class="float-end pagination">
                                            <ul class="pagination pagination-rounded">
                                                @for($i = 0; $i < $data['totalPages']; $i++)
                                                    <form action="{{route('monitoring-requests.store')}}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="card_number" value="{{ $card_number}}">
                                                        <input type="hidden" name="date_from" value="{{ $from }}">
                                                        <input type="hidden" name="date_to" value="{{ $to }}">
                                                        <input type="hidden" name="type" value="{{ $type }}">
                                                        <input type="hidden" name="page_number" value="{{ $i }}">
                                                    <li class="page-item {{ $i == $page_number ? 'active' : '' }}">
                                                        <button class="page-link" type="submit">{{ $i+1 }}</button>
                                                    </li>
                                                    </form>
                                                @endfor
                                        </ul>
                                    </div>
                                        @endif
                                @else
                                    <p>Нет данных для отображения.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/form-mask.init.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.copy-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const codeBlock = btn.parentElement.querySelector('code');
                    const textToCopy = codeBlock.innerText;

                    navigator.clipboard.writeText(textToCopy).then(() => {
                        btn.innerHTML = '<i class="fa fa-check"></i>';
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fa fa-copy"></i>';
                        }, 2000);
                    });
                });
            });
        });

        $(document).ready(function() {
            paginateTable("#custom_table", "#paginationContainer", 25);
        });
        function paginateTable(tableSelector, paginationContainer, rowsPerPage = 25) {
            const $table = $(tableSelector);
            const $rows = $table.find("tbody tr");
            let currentPage = 1;

            function displayRowsForPage(page) {
                const startIndex = (page - 1) * rowsPerPage;
                const endIndex   = startIndex + rowsPerPage;

                $rows.each(function(index) {
                    if (index >= startIndex && index < endIndex) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function createPagination() {
                const totalRows  = $rows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage);
                const $container = $(paginationContainer);

                $container.empty();

                if (totalPages <= 1) {
                    $rows.show();
                    return;
                }

                const maxVisible = 5;
                const $ul = $("<ul>").addClass("pagination pagination-rounded");

                function createPageItem(pageNum, isActive = false, isDisabled = false, text = null) {
                    const $li = $("<li>").addClass("page-item");
                    const $a  = $("<a>").addClass("page-link").attr("href", "#");

                    if (text) {
                        $a.text(text);
                    } else {
                        $a.text(pageNum);
                    }

                    if (isActive)   $li.addClass("active");
                    if (isDisabled) $li.addClass("disabled");

                    $a.on("click", function(e) {
                        e.preventDefault();
                        if (!isDisabled) {
                            currentPage = pageNum;
                            displayRowsForPage(currentPage);
                            createPagination();
                        }
                    });

                    $li.append($a);
                    return $li;
                }

                function addEllipsis() {
                    return createPageItem(currentPage, false, true, "…");
                }

                const $prevLi = createPageItem(currentPage - 1, false, currentPage <= 1, '<');
                $ul.append($prevLi);

                if (totalPages <= maxVisible + 2) {
                    for (let page = 1; page <= totalPages; page++) {
                        $ul.append(createPageItem(page, page === currentPage));
                    }
                } else {

                    $ul.append(createPageItem(1, currentPage === 1));

                    let startPage = currentPage - Math.floor(maxVisible / 2);
                    let endPage   = currentPage + Math.floor(maxVisible / 2);

                    if (startPage < 2) {
                        startPage = 2;
                        endPage   = startPage + (maxVisible - 1);
                    }
                    if (endPage >= totalPages) {
                        endPage   = totalPages - 1;
                        startPage = endPage - (maxVisible - 1);
                    }

                    if (startPage > 2) {
                        $ul.append(addEllipsis());
                    }

                    for (let page = startPage; page <= endPage; page++) {
                        $ul.append(createPageItem(page, page === currentPage));
                    }

                    if (endPage < totalPages - 1) {
                        $ul.append(addEllipsis());
                    }

                    $ul.append(createPageItem(totalPages, currentPage === totalPages));
                }

                const $nextLi = createPageItem(currentPage + 1, false, currentPage >= totalPages, '>');
                $ul.append($nextLi);

                $container.append($ul);
            }

            displayRowsForPage(currentPage);

            createPagination();
        }
    </script>
@endsection
