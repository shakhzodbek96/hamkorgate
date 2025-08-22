@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet"/>
    <style>
        pre {
            overflow: auto; /* Добавляем прокрутку для длинного кода */
            padding: 1.5rem; /* Отступы для удобного чтения */
        }

        .copy-btn {
            cursor: pointer;
        }

        .copy-btn i {
            font-size: 1rem;
        }

    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Запросы на платежи</h4>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск
                @if($total != null)
                    <sup class="badge badge-soft-primary">{{ number_format($total) }}</sup>
                @endif
            </h5>
            <div class="card">
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-2">
                                <label>Пинфл</label>
                                <input type="text" name="pinfl" class="form-control" value="{{ request()->pinfl }}"
                                       maxlength="14">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>EXT</label>
                                <input type="text" name="ext" class="form-control" value="{{ request()->ext }}"
                                       maxlength="150">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Контракт ID</label>
                                <input type="text" name="loan_id" class="form-control" value="{{ request()->loan_id }}"
                                       maxlength="150">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Процессинг</label>
                                <select name="processing" class="form-select">
                                    <option value="">Выбрать</option>
                                    <option value="sv" {{ request()->processing == 'sv' ? 'selected' : '' }}>SV</option>
                                    <option value="humo" {{ request()->processing == 'humo' ? 'selected' : '' }}>HUMO
                                    </option>
                                </select>
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                @if(\App\Services\Helpers\Check::isAdmin())
                                    <label>Выберите партнера</label>
                                    <select class="form-select select-search-partner" name="partner_id">
                                        @if(request()->partner_id)
                                            <option value="{{ request()->partner_id }}" selected>
                                            {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                        @endif
                                    </select>
                                    <input type="hidden" name="partner_id_operator" value="=">
                                @else
                                    <label>Выберите Статус</label>
                                    <select class="form-select" name="status">
                                        <option value="">Все</option>
                                        <option value="success" {{request()->status == 'success' ? 'selected' : ''}}>Успешный</option>
                                        <option value="failed" {{request()->status == 'failed' ? 'selected' : ''}}>Ошибка</option>
                                        <option value="cancelled" {{request()->status == 'cancelled' ? 'selected' : ''}}>Отменен</option>
                                        <option value="cancel_failed" {{request()->status == 'cancel_failed' ? 'selected' : ''}}>Отмена не удалась</option>
                                    </select>
                                @endif
                            </div>

                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('transaction-requests.export',request()->all()) }}"
                                       class="btn btn-success"><i class="fa fa-file-excel"></i></a>
                                    <a href="{{ route('transaction-requests.index') }}"
                                       class="btn btn-warning btn-rounded">
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
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">ПИНФЛ</th>
                                <th class="align-middle">Контракт ID</th>
                                <th class="align-middle text-end">Сумма</th>
                                <th class="align-middle">Тип</th>
                                <th class="align-middle">Статус</th>
                                <th class="align-middle">Дата</th>
                                <th class="align-middle">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td class="align-middle">{{ $transaction->pinfl }}</td>
                                    <td class="align-middle">{{ $transaction->loan_id }}</td>
                                    <td class="align-middle text-end">{{ number_format($transaction->amount/100,2,'.') }}</td>
                                    <td class="align-middle">
                                        @if($transaction->processing == 'sv')
                                            <span class="badge badge-soft-info">SV</span>
                                        @else
                                            <span class="badge badge-soft-success">HUMO</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'cancelled' => ['class' => 'badge-soft-danger', 'text' => 'Отменен'],
                                                'cancel_failed' => ['class' => 'badge-soft-primary', 'text' => 'Отмена не удалась'],
                                                'success'=> ['class' => 'badge-soft-success', 'text' => 'Успешно'],
                                                'failed'=> ['class' => 'badge-soft-secondary', 'text' => 'Ошибка'],
                                            ];
                                            $current = $statusMap[$transaction->status] ?? ['class' => 'badge-soft-warning', 'text' => 'Нет статуса'];
                                        @endphp

                                        <span class="badge {{ $current['class'] }}">{{ $current['text'] }}</span>
                                    </td>
                                    <td class="align-middle">{{ $transaction->created_at }}</td>
                                    <td class="align-middle">
                                        <div class="btn-group dropend">
                                            @if(\App\Services\Helpers\Check::isAdmin())
                                                <a class="btn btn-primary btn-sm" type="button"
                                                   data-bs-toggle="offcanvas"
                                                   data-bs-target="#codeFormat{{ $transaction->id }}"
                                                   aria-controls="offcanvasRight">
                                                    <i class="fas fa-info"></i>
                                                    <span>Детали</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('transaction-requests.show', $transaction->ext) }}"
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-sync"></i>
                                                Проверить статус
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @if(\App\Services\Helpers\Check::isAdmin())
                                    <div class="offcanvas offcanvas-end w-50" tabindex="-1"
                                         id="codeFormat{{ $transaction->id }}"
                                         aria-labelledby="offcanvasRightLabel" aria-hidden="true"
                                         style="visibility: hidden;">
                                        <div class="offcanvas-header">
                                            <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Детали <br> <b
                                                    class="text-muted font-size-12">{{ $transaction->ext }}</b></h5>
                                            <button type="button" class="btn-close text-reset"
                                                    data-bs-dismiss="offcanvas"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <div class="row">
                                                @if($transaction->merchant && $transaction->terminal)
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="mb-2 fw-bold font-size-16">Терминал</label>
                                                            <input type="text" disabled
                                                                   value="{{ $transaction->terminal }}"
                                                                   class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="mb-2 fw-bold font-size-16">Мерчант</label>
                                                            <input type="text" disabled
                                                                   value="{{ $transaction->merchant }}"
                                                                   class="form-control">
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($transaction->rrn)
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="mb-2 fw-bold font-size-16">RRN</label>
                                                            <div
                                                                class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                                <button
                                                                    class="copy-btn btn btn-sm btn-outline-secondary ms-2"
                                                                    title="Скопировать">
                                                                    <i class="fa fa-copy"></i>
                                                                </button>
                                                                <pre class="mb-2 flex-grow-1 bg-dark"><code
                                                                        class="language-json">{{ $transaction->rrn }}</code></pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($transaction->request)
                                                    <div class="col-12 mb-3">
                                                        <label class="mb-2 fw-bold font-size-16">Запрос</label>
                                                        <div
                                                            class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                            <button
                                                                class="copy-btn btn btn-sm btn-outline-secondary ms-2"
                                                                title="Скопировать">
                                                                <i class="fa fa-copy"></i>
                                                            </button>
                                                            <pre class="mb-2 flex-grow-1 bg-dark"><code
                                                                    class="language-json">{{ json_encode($transaction->request, JSON_PRETTY_PRINT) }}</code></pre>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($transaction->response)
                                                    <div class="col-12 mb-3">
                                                        <label class="mb-2 fw-bold font-size-16">Ответ</label>
                                                        <div
                                                            class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                            <button
                                                                class="copy-btn btn btn-sm btn-outline-secondary ms-2"
                                                                title="Скопировать">
                                                                <i class="fa fa-copy"></i>
                                                            </button>
                                                            <pre class="mb-2 flex-grow-1 bg-dark"><code
                                                                    class="language-json">{{ json_encode($transaction->response, JSON_PRETTY_PRINT) }}</code></pre>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-3">
                        {{ $transactions->withQueryString()->links() }}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.copy-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const codeBlock = btn.parentElement.querySelector('code');
                    const textToCopy = codeBlock.innerText;

                    navigator.clipboard.writeText(textToCopy).then(() => {
                        btn.innerHTML = '<i class="fa fa-check"></i>'; // Иконка подтверждения
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fa fa-copy"></i>'; // Вернуть иконку копирования
                        }, 2000);
                    });
                });
            });
        });
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

    </script>
@endsection
