@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Контракты</h4>
                <div class="page-title-right btn-group-sm">
                    <a href="{{ route('contract-stats.index',request()->all()) }}"
                       class="btn btn-outline-secondary waves-effect waves-light me-1">
                        <i class="fa fa-arrow-left align-middle font-size-16"></i> Назад к контрактам
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5 class="card-title pb-2">
                Поиск <sup class="badge badge-soft-primary" style="margin-right: 15px">{{ $contracts->total() }}</sup>
            </h5>
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row justify-content-evenly">
                            <div class="cols-sm-12 col-lg-3 mb-3">
                                    <label>Выберите партнера</label>
                                    <select class="form-select select-search-partner" name="partner_id">
                                        @if(request()->partner_id)
                                            <option value="{{ request()->partner_id }}" selected>
                                            {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                        @endif
                                    </select>
                                </div>
                            <div class="col-sm-12 col-lg-3 mb-2">
                                <label>Кол-карты</label>
                                <select name="cards_count" class="form-select">
                                    <option value="">Все</option>
                                    <option value="1" @if(request()->cards_count == '1') selected @endif>
                                        Без карты (Узкард)
                                    </option>
                                    <option value="2" @if(request()->cards_count == '2') selected @endif>
                                        Без карты (Хумо)
                                    </option>
                                    <option value="0" @if(request()->cards_count === '0') selected @endif>
                                        Без единой карты
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-lg-3 mb-2">
                                <label>Кол-запросов</label>
                                <select name="request_count" class="form-select">
                                    <option value="">Все</option>
                                    <option value="1" @if(request()->request_count == '1') selected @endif>
                                        Без поиска (Узкард)
                                    </option>
                                    <option value="2" @if(request()->request_count == '2') selected @endif>
                                        Без поиска (Хумо)
                                    </option>
                                    <option value="0" @if(request()->request_count === '0') selected @endif>
                                        Без единого поиска
                                    </option>
                                </select>
                            </div>
                            <input type="hidden" name="partner_id_operator" value="=">
                            <input type="hidden" name="merchant_id_operator" value="=">
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn-rounded btn btn-primary">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('contract-stats.index-group-by') }}" class="btn-rounded btn btn-warning">
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
                        <table class="table align-middle table-check myDt">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">Партнер</th>
                                <th class="align-middle">Кол-контрактов</th>
                                <th class="align-middle">Кол-карты</th>
                                <th class="align-middle">Кол-запросов</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    <td class="fw-muted">
                                        {{ $contract->partner_name}}
                                    </td>
                                    <td>
                                        <strong>{{ $contract->count }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-info">uzcard: </span><strong>{{ $contract->sv_cards }}</strong><br>
                                        <span class="text-warning">humo: </span><strong>{{ $contract->humo_cards }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-info">uzcard: </span><strong>{{ $contract->sv_requests }}</strong><br>
                                        <span class="text-warning">humo: </span><strong>{{ $contract->humo_requests }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $contracts->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
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
                }
            });
        });
    </script>
@endsection
