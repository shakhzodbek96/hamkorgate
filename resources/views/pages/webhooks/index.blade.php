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
                <h4 class="mb-sm-0 font-size-18">Запросы на платежи</h4>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-primary">{{ number_format($webhooks->total()) }}</sup>
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
                                <label>EXT</label>
                                <input type="text" name="ext" class="form-control" value="{{ request()->ext }}" maxlength="150">
                            </div>
                                <div class="cols-sm-12 col-lg-2">
                                    <label class="form-label">Статус</label>
                                    <select class="form-select" name="is_sent">
                                        <option value="">Все</option>
                                        <option value="true" {{ request()->is_sent == 'true' ? 'selected':'' }} > Успешная</option>
                                        <option value="false" {{ request()->is_sent == 'false' ? 'selected':'' }}>Не отправлено</option>
                                    </select>
                                </div>
                            <input type="hidden" name="partner_id_operator" value="=">
                            <input type="hidden" name="is_sent_operator" value="=">

                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('webhooks.index') }}" class="btn btn-warning btn-rounded">
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
                                <th class="align-middle">EXT</th>
                                <th class="align-middle">Статус Отправки</th>
                                <th class="align-middle">HTTP Статус</th>
                                <th class="align-middle">Попытки</th>
                                <th class="align-middle">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($webhooks as $webhook)
                                <tr>
                                    @if(\App\Services\Helpers\Check::isAdmin())
                                        <td class="align-middle">{{ $webhook->partner->name ?? '---' }}</td>
                                    @endif
                                    <td class="align-middle">{{ $webhook->url ?? '---' }}</td>
                                    <td class="align-middle">
                                        {{ $webhook->ext ?? '---' }}
                                        <br>
                                        <span class="text-muted mb-0 font-size-10">
                                            created: {{ $webhook->created_at }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-pill {{ $webhook->is_sent ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $webhook->is_sent ? 'Отправлено' : 'Не отправлено' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-pill
                                            @if(in_array($webhook->http_status, [200, 201, 202]))
                                                badge-soft-success
                                            @elseif(in_array($webhook->http_status, [401, 403, 404]))
                                                badge-soft-danger
                                            @elseif($webhook->http_status == 429)
                                                badge-soft-warning
                                            @else
                                                badge-soft-secondary
                                            @endif">
                                            {{ $webhook->http_status ?? '---' }}
                                        </span>
                                        <br>
                                        <span class="text-muted mb-0 font-size-10">
                                            {{ $webhook->last_attempt_at ?? 'No attempts yet' }}
                                        </span>
                                    </td>

                                    <td class="align-middle">{{ $webhook->tries }}</td>

                                    <td class="align-middle">
                                        <div class="btn-group dropend">
                                            <form action="{{ route('webhooks.resend', $webhook->id )}}" method="POST">
                                                @csrf
                                                @if($webhook->is_sent)
                                                    <button class="btn btn-success btn-sm waves-effect waves-light"  type="submit" onclick="return confirm('Are you sure you want to submit this action?');">
                                                        <i class="fas fa-redo-alt"></i>
                                                        <span> Повтор</span>
                                                    </button>
                                                @else
                                                    <button class="btn btn-success btn-sm waves-effect waves-light"  type="submit">
                                                        <i class="fas fa-redo-alt"></i>
                                                        <span> Повтор</span>
                                                    </button>
                                                @endif
                                            </form>
                                            <a class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#codeFormat{{ $webhook->id }}" aria-controls="offcanvasRight">
                                                <i class="fas fa-info"></i>
                                                <span>Детали</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="codeFormat{{ $webhook->id }}"
                                     aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Детали <br> <b class="text-muted font-size-12"> Токен: {{ $webhook->token }}</b></h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <div class="row">
                                            @if($webhook->request)
                                                <div class="col-12 mb-3">
                                                    <label class="mb-2 fw-bold font-size-16">Запрос</label>
                                                    <div class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                        <button class="copy-btn btn btn-sm btn-outline-secondary ms-2" title="Скопировать">
                                                            <i class="fa fa-copy"></i>
                                                        </button>
                                                        <pre class="mb-2 flex-grow-1 bg-dark"><code class="language-json">{{ json_encode($webhook->request, JSON_PRETTY_PRINT) }}</code></pre>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($webhook->response)
                                                <div class="col-12 mb-3">
                                                    <label class="mb-2 fw-bold font-size-16">Ответ</label>
                                                    <div class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                        <button class="copy-btn btn btn-sm btn-outline-secondary ms-2" title="Скопировать">
                                                            <i class="fa fa-copy"></i>
                                                        </button>
                                                        <pre class="mb-2 flex-grow-1 bg-dark"><code class="language-json">{{ json_encode($webhook->response, JSON_PRETTY_PRINT) }}</code></pre>
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
                        {{ $webhooks->withQueryString()->links() }}
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
