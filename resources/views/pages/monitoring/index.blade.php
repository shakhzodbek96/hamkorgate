@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Мониторинг</h4>

              <div class="btn">
                  @can('Мониторинг карт Humo')
                      <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                              data-bs-target="#monitoringHumo">
                          <i class="fas fa-history"></i> Мониторинг Humo
                      </button>
                      <div class="modal fade" id="monitoringHumo" tabindex="-1"
                           role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title">
                                          Мониторинг карт Humo
                                      </h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal"
                                              aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                      <form action="{{ route('monitoring-requests.store') }}" method="post">
                                          @csrf
                                          <input type="hidden" name="type" value="humo">
                                          <div class="form-group mb-5">
                                              <label for="card_number">Номер карты</label>
                                              <input id="input-mask" class="form-control input-mask" name="card_number"
                                                     data-inputmask="'mask': '9999 9999 9999 9999'" placeholder="____ ____ ____ ____" value="{{ old('card_number') }}">
                                          </div>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group mb-5">
                                                      <label for="date_from">Дата от</label>
                                                      <input id="input-date1" class="form-control input-mask"
                                                             data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="dd-mm-yyyy" placeholder="dd-mm-yyyy" name="date_from" value="{{ old('date_from') }}">
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group mb-5">
                                                      <label for="date_to">Дата до</label>
                                                      <input id="input-date1" class="form-control input-mask"
                                                             data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="dd-mm-yyyy" placeholder="dd-mm-yyyy" value="{{ old('date_to', now()->format('d-m-Y')) }}" name="date_to">
                                                  </div>
                                              </div>
                                              <div class="col-lg-12">
                                                  <div class="mb-3">
                                                      <button type="submit" class="btn btn-primary w-100">Получить отчет</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </form>
                                  </div>
                              </div><!-- /.modal-content -->
                          </div><!-- /.modal-dialog -->
                      </div>
                  @endcan
                  @can('Мониторинг карт Uzcard')
                      <button type="button" class="btn btn-outline-primary mx-2" data-bs-toggle="modal"
                              data-bs-target="#monitoringUzcard">
                          <i class="fa fa-history"></i> Мониторинг Uzcard
                      </button>
                      <div class="modal fade" id="monitoringUzcard" tabindex="-1"
                           role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title">
                                          Мониторинг карт Uzcard
                                      </h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal"
                                              aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                      <form action="{{ route('monitoring-requests.store') }}" method="post">
                                          @csrf
                                          <input type="hidden" name="type" value="uzcard">
                                          <div class="form-group mb-5">
                                              <label for="card_number">Номер карты</label>
                                              <input id="input-mask" class="form-control input-mask" name="card_number"
                                                     data-inputmask="'mask': '9999 9999 9999 9999'" placeholder="____ ____ ____ ____" value="{{ old('card_number', request()->card_number) }}">
                                          </div>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group mb-5">
                                                      <label for="date_from">Дата от</label>
                                                      <input id="input-date1" class="form-control input-mask"
                                                             data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="dd-mm-yyyy" placeholder="dd-mm-yyyy" name="date_from" value="{{ old('date_from', request()->date_from) }}">
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group mb-5">
                                                      <label for="date_to">Дата до</label>
                                                      <input id="input-date1" class="form-control input-mask"
                                                             data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="dd-mm-yyyy" placeholder="dd-mm-yyyy" value="{{ old('date_to', now()->format('d-m-Y')) }}" name="date_to">
                                                  </div>
                                              </div>
                                              <div class="col-lg-12">
                                                  <div class="mb-3">
                                                      <button type="submit" class="btn btn-primary w-100">Получить отчет</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </form>
                                  </div>
                              </div><!-- /.modal-content -->
                          </div><!-- /.modal-dialog -->
                      </div>
                  @endcan

                  @can('Регистрация карты')
                      <a href="{{ route('monitoring-requests.register') }}" class="btn btn-info">
                          <i class="fas fa-sms font-size-14"></i> Проверка OTP
                      </a>
                  @endcan
              </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title pb-2">
                        Поиск
                    </h5>
                    <div class="card">
                        <div class="card-body">
                            <form class="justify-content-end">
                                <div class="row">
                                    <div class="cols-sm-12 col-lg-2">
                                        <label>Номер карты</label>
                                        <input id="input-mask" class="form-control input-mask" name="card_number" type="text"
                                               data-inputmask="'mask': '9999999999999999'" placeholder="____ ____ ____ ____" maxlength="16"
                                               value="{{ request()->card_number }}">
                                    </div>
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
                                    <div class="col-sm-12 col-lg-2">
                                        <label>Статус</label>
                                        <input type="hidden" name="status_operator" value="=">
                                        <select name="status" class="form-select">
                                            <option value="">Все</option>
                                            <option value="success" @if(request()->status == 'success') selected @endif>Успешно</option>
                                            <option value="failed" @if(request()->status == 'failed') selected @endif>Ошибка</option>
                                            <option value="created" @if(request()->status == 'created') selected @endif>Создан</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-lg-2">
                                        <label>Тип</label>
                                        <input type="hidden" name="card_type_operator" value="=">
                                        <select name="card_type" class="form-select">
                                            <option value="">Все</option>
                                            <option value="uzcard" @if(request()->type == 'uzcard') selected @endif>Uzcard</option>
                                            <option value="humo" @if(request()->type == 'humo') selected @endif>Humo</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="partner_id_operator" value="=">
                                    <div class="col-sm-12 col-lg-2">
                                        <div class="btn-group w-100 mt-4" role="group">
                                            <button type="submit" class="btn-rounded btn btn-primary">
                                                <i class="fas fa-search font-size-14"></i>
                                            </button>
                                            <a href="{{ route('monitoring-requests.index') }}" class="btn-rounded btn btn-warning">
                                                <i class="fas fa-sync font-size-14"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <x-alert-success/>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex flex-wrap">
                                <h4 class="card-title mb-4">Отчет по запросам на мониторинг <span class="badge badge-soft-primary"> {{ $monitoring_requests->total() }}</span></h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        @if(\App\Services\Helpers\Check::isAdmin())
                                            <th class="align-middle">Партнер</th>
                                        @endif
                                        <th class="align-middle">Номер карты</th>
                                        <th class="align-middle">Статус</th>
                                        <th class="align-middle">Тип карты</th>
                                        <th class="align-middle">Дата</th>
                                        <th class="align-middle">Запрос от</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($monitoring_requests as $request)
                                        <tr>
                                            @if(\App\Services\Helpers\Check::isAdmin())
                                                <td>
                                                    {{ $request->partner_name ?? 'Администратор' }}
                                                </td>
                                            @endif
                                            <td>
                                                {{ $request->card_number }}
                                            </td>
                                            <td>
                                                @if($request->status == 'success')
                                                    <span class="badge badge-soft-success">Успешно</span>
                                                @elseif($request->status == 'failed')
                                                    <span class="badge badge-soft-danger">Ошибка</span>
                                                @elseif($request->status == 'created')
                                                    <span class="badge badge-soft-info">Создан</span>
                                                @else
                                                    <span class="badge badge-soft-warning">Неизвестно</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($request->card_type == 'uzcard')
                                                    <span class="badge badge-soft-info">Uzcard</span>
                                                @elseif($request->card_type == 'humo')
                                                    <span class="badge badge-soft-info">Humo</span>
                                                @else
                                                    <span class="badge badge-soft-info">Другое</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $request->created_at->format('H:i:s / d.m.Y') }}
                                            </td>
                                            <td>{{ $request->user_name??'API'}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                {{ $monitoring_requests->withQueryString()->links() }}
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
                        btn.innerHTML = '<i class="fa fa-check"></i>'; // Иконка подтверждения
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fa fa-copy"></i>'; // Вернуть иконку копирования
                        }, 2000);
                    });
                });
            });
        });

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
    </script>
@endsection
