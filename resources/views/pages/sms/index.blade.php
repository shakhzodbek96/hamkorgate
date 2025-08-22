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
                <h4 class="mb-sm-0 font-size-18">СМС на платежи</h4>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-primary">{{ $sms->total() }}</sup>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form class="justify-content-end">
                        <div class="row">
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <div class="cols-sm-12 col-lg-3">
                                    <label>Выберите партнера</label>
                                    <select class="form-select select-search-partner" name="partner_id">
                                        @if(request()->partner_id)
                                            <option value="{{ request()->partner_id }}" selected>
                                            {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                        @endif
                                    </select>
                                </div>
                            @endif

                            <div class="cols-sm-12 col-lg-3">
                                <label>EXT</label>
                                <input type="text" name="ext_id" class="form-control" value="{{ request()->ext_id }}" maxlength="150">
                            </div>
                            <div class="cols-sm-12 col-lg-3">
                                <label class="form-label">Статус Отправки</label>
                                <select class="form-select" name="is_sent">
                                    <option value="">Все</option>
                                    <option value="1" {{ request()->is_sent == '1' ? 'selected':'' }} > Успешная</option>
                                    <option value="0" {{ request()->is_sent == '0' ? 'selected':'' }}>Не отправлено</option>
                                </select>
                            </div>
                            <input type="hidden" name="partner_id_operator" value="=">
                            <input type="hidden" name="is_sent_operator" value="=">

                            <div class="col-sm-12 col-lg-3">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('sms.index') }}" class="btn btn-warning btn-rounded">
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
                                <th class="align-middle">Телефон</th>
                                <th class="align-middle">EXT</th>
                                <th class="align-middle">Статус Отправки</th>
                                <th class="align-middle">HTTP Код</th>
                                <th class="align-middle">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sms as $data)
                                <tr>
                                    <td class="align-middle">
                                        <p class="mb-0">
                                            {{ $data->pinfl ?? '---' }}
                                        </p>
                                        @if(\App\Services\Helpers\Check::isAdmin())
                                            <a href="{{route('partners.index', ['name' => $data->partner_name])}}">
                                                {{ $data->partner_name ?? '---' }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <a href="tel:{{ \App\Services\Helpers\Helper::phoneFormat($data->phone) }}">
                                            {{ \App\Services\Helpers\Helper::phoneShowFormatting($data->phone) }}
                                        </a>
                                        <br>
                                        <span class="text-muted mb-0 font-size-10">
                                            created: {{ $data->created_at }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted mb-0 font-size-10">
                                            {{$data->ext_id}}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-pill {{ $data->is_sent ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $data->is_sent ? 'Отправлено' : 'Не отправлено' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-pill
                                            @if(in_array($data->http_code, [200, 201, 202]))
                                                badge-soft-success
                                            @elseif(in_array($data->http_code, [401, 403, 404]))
                                                badge-soft-danger
                                            @elseif($data->http_code == 429)
                                                badge-soft-warning
                                            @else
                                                badge-soft-secondary
                                            @endif">
                                            {{ $data->http_code ?? '---' }}
                                        </span>
                                    </td>


                                    <td class="align-middle">
                                        <a href="{{ route('sms.send',$data->id) }}" class="btn btn-success btn-sm {{ $data->is_sent ? 'disabled':'' }}"><i class="bx bx-envelope align-middle font-size-14"></i> Отправить</a>
                                        <div class="btn-group dropend">
                                            <a class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#codeFormat{{ $data->id }}" aria-controls="offcanvasRight">
                                                <i class="fas fa-info"></i>
                                                <span>Детали</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="codeFormat{{ $data->id }}"
                                     aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Детали</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <div class="row">
                                            @if($data->message)
                                                <div class="col-12 mb-3">
                                                    <label class="mb-2 fw-bold font-size-16">Сообщение</label>
                                                    <blockquote class="p-4 border-light border rounded mb-4">
                                                        <div class="d-flex">
                                                            <div class="me-3">
                                                                <i class="bx bxs-quote-alt-left text-dark font-size-24"></i>
                                                            </div>
                                                            <div>
                                                                <p class="mb-0"> {{$data->message}}</p>
                                                            </div>
                                                        </div>
                                                    </blockquote>
                                                </div>
                                            @endif
                                                @if($data->response)
                                                    <div class="col-12 mb-3">
                                                        <label class="mb-2 fw-bold font-size-16">Ответ</label>
                                                        <div class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                            <button class="copy-btn btn btn-sm btn-outline-secondary ms-2" title="Скопировать">
                                                                <i class="fa fa-copy"></i>
                                                            </button>
                                                            <pre class="mb-2 flex-grow-1 bg-dark"><code class="language-json">{{ json_encode($data->response, JSON_PRETTY_PRINT) }}</code></pre>
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
                        {{ $sms->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-advanced.init.js') }}"></script>
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
