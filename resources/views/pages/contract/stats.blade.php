@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Статистика контракта <span class="text-primary">{{ $contract->pinfl }} <i class="font-size-12">({{$contract->loan_id}})</i></span></h4>
                <div class="page-title-right btn-group">
                    <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-long-arrow-alt-left"></i>
                        Назад к контракту
                    </a>
                </div>
            </div>
            <x-alert-success/>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                                <h4 class="font-size-16 fw-bold">Запросы на карт <span class="badge badge-soft-primary">{{ $pinfl_requests->total() }}</span></h4>
                                <div class="btn-group-sm">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>Тип</th>
                                        <th>Количество карт</th>
                                        <th>Последнее проведение</th>
                                        <th>Дата создания</th>
                                        <th>Статус</th>
                                        <th>Исполнитель</th>
                                        @if(\App\Services\Helpers\Check::isAdmin())
                                            <th>Действие</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pinfl_requests as $request)
                                        <tr>
                                            <td>
                                                @if($request->type == 'uzcard')
                                                    <span class="badge badge-soft-info">Uzcard</span>
                                                @else
                                                    <span class="badge badge-soft-warning">Humo</span>
                                                @endif
                                            </td>
                                            <td>{{ $request->cards_count }}</td>
                                            <td>{{ $request->processed_at ?? '---'}}</td>
                                            <td>
                                                {{ $request->created_at }}
                                            </td>
                                            <td>
                                                @if($request->status == 'success')
                                                    <span class="badge badge-soft-success">Успешный</span>
                                                @elseif($request->status == 'created')
                                                    <span class="badge badge-soft-info">Создан</span>
                                                @elseif($request->status == 'processing')
                                                    <span class="badge badge-soft-warning">В процессе</span>
                                                @elseif($request->status == 'failed')
                                                    <span class="badge badge-soft-danger">Ошибка</span>
                                                @else
                                                    <span class="badge badge-soft-danger">Неизвестно</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $request->user_name ?? 'API' }}
                                                <br>
                                                <span class="font-size-10">{{ $request->user_email ?? '' }}</span>
                                            </td>
                                            @if(\App\Services\Helpers\Check::isAdmin())
                                                <td class="align-middle">
                                                    <div class="btn-group dropend">
                                                        <a class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#codeFormat{{ $request->id }}" aria-controls="offcanvasRight">
                                                            <i class="fas fa-info"></i>
                                                            <span>Детали</span>
                                                        </a>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                        <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="codeFormat{{ $request->id }}"
                                             aria-labelledby="offcanvasRightLabel" aria-hidden="true" style="visibility: hidden;">
                                            <div class="offcanvas-header">
                                                <h5 id="offcanvasRightLabel" class="text-primary fw-bold">Детали</h5>
                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                <div class="row">
                                                    @if($request->success_response && \App\Services\Helpers\Check::isAdmin())
                                                        <div class="col-12 mb-3">
                                                            <label class="mb-2 fw-bold font-size-16 text-success"> Успешный </label>
                                                            <div class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                                <button class="copy-btn btn btn-sm btn-outline-secondary ms-2" title="Скопировать">
                                                                    <i class="fa fa-copy"></i>
                                                                </button>
                                                                <pre class="mb-2 flex-grow-1 bg-dark"><code class="language-json">{{ json_encode($request->success_response, JSON_PRETTY_PRINT) }}</code></pre>
                                                            </div>
                                                        </div>
                                                    @endif
                                                        @if($request->failed_response && \App\Services\Helpers\Check::isAdmin())
                                                            <div class="col-12 mb-3">
                                                                <label class="mb-2 fw-bold font-size-16 text-danger"> Ошибка</label>
                                                                <div class="d-flex align-items-start flex-column bg-dark p-3 rounded overflow-scroll">
                                                                    <button class="copy-btn btn btn-sm btn-outline-secondary ms-2" title="Скопировать">
                                                                        <i class="fa fa-copy"></i>
                                                                    </button>
                                                                    <pre class="mb-2 flex-grow-1 bg-dark"><code class="language-json">{{ json_encode($request->failed_response, JSON_PRETTY_PRINT) }}</code></pre>
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
                        </div>
                        <div class="card-footer mt-3">
                            {{ $pinfl_requests->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- form mask -->
    <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>

    <!-- form mask init -->
    <script src="{{ URL::asset('/assets/js/pages/form-mask.init.js') }}"></script>

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
    </script>
@endsection
