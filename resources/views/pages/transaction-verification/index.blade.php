@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
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
                <h4 class="mb-sm-0 font-size-18">Верификации транзакций <b>API</b></h4>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск @if($total != null) <sup class="badge badge-soft-primary">{{ number_format($total) }}</sup> @endif
            </h5>
            <div class="card">
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                           @if(\App\Services\Helpers\Check::isAdmin())
                                <div class="cols-sm-12 col-lg-2">
                                    <label>Выберите партнера</label>
                                    <select class="form-select select-search-partner" name="partner_id">
                                        @if(request()->partner_id)
                                            <option value="{{ request()->partner_id }}" selected>
                                            {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                        @endif
                                    </select>
                                </div>
                           @endif

                            <div class="cols-sm-12 col-lg-2">
                                <label>Лоан-ИД</label>
                                <input type="text" name="loan_id" class="form-control" value="{{ request()->loan_id }}" maxlength="150">
                            </div>
                           <div class="cols-sm-12 col-lg-2">
                               <label>HTTP Code</label>
                               <input type="text" name="http_code" class="form-control" value="{{ request()->http_code }}" maxlength="3">
                               <input hidden name="http_code_operator" value="=">
                           </div>
                           <input type="hidden" name="partner_id_operator" value="=">
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('transaction-verification.index') }}" class="btn btn-warning btn-rounded">
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
                                @if(\App\Services\Helpers\Check::isAdmin())
                                    <th class="align-middle">Партнер</th>
                                @endif
                                <th class="align-middle">URL</th>
                                <th class="align-middle">Лоан-ИД</th>
                                <th class="align-middle">HTTP Статус</th>
                                <th class="align-middle">Код Ответа</th>
                                <th class="align-middle">Время Соединения / Отклика</th>
                                <th class="align-middle">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactionVerifications as $result)
                                <tr>
                                    @if(\App\Services\Helpers\Check::isAdmin())
                                        <td class="align-middle">{{ $result->partner->name ?? '---' }}</td>
                                    @endif
                                    <td class="align-middle">{{ $result->host ?? '---' }}</td>
                                    <td class="align-middle">{{ $result->loan_id ?? '---' }}</td>
                                    <td class="align-middle">
                                        <span class="badge badge-pill
                                            @if(in_array($result->http_code, [200, 201, 202]))
                                                badge-soft-success
                                            @elseif(in_array($result->http_code, [401, 403, 404]))
                                                badge-soft-danger
                                            @elseif($result->http_code == 429)
                                                badge-soft-warning
                                            @else
                                                badge-soft-secondary
                                            @endif">
                                            {{ $result->http_code ?? '---' }}
                                        </span>
                                        <br>
                                        <span class="text-muted mb-0 font-size-10">
                                            {{ $result->created_at ?? 'No attempts yet' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-pill
                                            @if(($result->response['code'] ?? '') == 100)
                                                badge-soft-success
                                            @elseif(($result->response['code'] ?? '') == 101)
                                                badge-soft-danger
                                            @elseif(($result->response['code'] ?? '') == 102)
                                                badge-soft-warning
                                            @else
                                                badge-soft-secondary
                                            @endif">
                                            {{ $result->response['code'] ?? '---' }}
                                        </span>
                                    </td>

                                    <td class="align-middle">
                                        {{ number_format($result->connection_time * 100, 3)}} <sub class="text-muted">ms</sub> / {{ number_format($result->response_time * 100, 3) }} <sub class="text-muted">ms</sub>
                                    </td>

                                    <td class="align-middle">
                                        <div class="btn-group dropend">
                                            <a class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#codeFormat{{ $result->id }}" aria-controls="offcanvasRight">
                                                <i class="fas fa-info"></i>
                                                <span>Детали</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="codeFormat{{ $result->id }}"
                                     aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Детали <br> <b class="text-muted font-size-12"> Токен: {{ $result->token }}</b></h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <div class="row">
                                            @if($result->request)
                                                <div class="col-12 mb-3">
                                                    <label class="mb-2 fw-bold font-size-16">Запрос</label>
                                                    <div class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                        <button class="copy-btn btn btn-sm btn-outline-secondary ms-2" title="Скопировать">
                                                            <i class="fa fa-copy"></i>
                                                        </button>
                                                        <pre class="mb-2 flex-grow-1 bg-dark"><code class="language-json">{{ json_encode($result->request, JSON_PRETTY_PRINT) }}</code></pre>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($result->response)
                                                <div class="col-12 mb-3">
                                                    <label class="mb-2 fw-bold font-size-16">Ответ</label>
                                                    <div class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                        <button class="copy-btn btn btn-sm btn-outline-secondary ms-2" title="Скопировать">
                                                            <i class="fa fa-copy"></i>
                                                        </button>
                                                        <pre class="mb-2 flex-grow-1 bg-dark"><code class="language-json">{{ json_encode($result->response, JSON_PRETTY_PRINT) }}</code></pre>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-3">
                        {{ $transactionVerifications->withQueryString()->links() }}
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
                },
            });
        });
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
    </script>
@endsection
