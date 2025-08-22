@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Партнеры</h4>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <x-alert-success/>
                <div class="card-body">
                    <h4 class="card-title">
                        Поиск <sup class="text-primary">{{ $stats->total() }}</sup>
                    </h4>
                    <form>
                        <div class="row justify-content-end align-items-center">
                            @if(auth()->user()->is_admin)
                                <div class="cols-sm-12 col-lg-2 mb-3">
                                    <label>Выберите партнера</label>
                                    <select class="form-select select-search-partner" name="partner_id">
                                        @if(request()->partner_id)
                                            <option value="{{ request()->partner_id }}" selected>
                                            {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                        @endif
                                    </select>
                                </div>
                            @endif
                            <div class="col-12 col-lg-4 mb-3">
                                <div class="row ">
                                    <label class="form-label">Дата</label>
                                    <div class="col-4 col-sm-2">
                                        <select class="form-select" name="date_operator" onchange="
                                                        if(this.value === 'between')
                                                            document.getElementById('date_pair').style.display = 'block';
                                                        else
                                                            document.getElementById('date_pair').style.display = 'none';">
                                            <option value="=" {{ request()->date_operator == '=' ? 'selected':'' }}>=</option>
                                            <option value=">" {{ request()->date_operator == '>' ? 'selected':'' }}>&gt;</option>
                                            <option value="<" {{ request()->date_operator == '<' ? 'selected':'' }}>&lt;</option>
                                            <option value="between" {{ request()->date_operator == 'between' ? 'selected':'' }}>&#8596;</option>
                                        </select>
                                    </div>
                                    <div class="col-8 col-sm-5">
                                        <input class="form-control" type="month" name="date" value="{{ old('date',request()->date??'') }}">
                                    </div>
                                    <div class="col-12 col-sm-5" id="date_pair"
                                         style="display: {{ request()->date_operator == 'between' ? 'block':'none'}}">
                                        <input class="form-control" type="month" name="date_pair" value="{{ old('date_pair',request()->date_pair??'') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('partners.stats') }}" class="btn btn-warning btn-rounded">
                                        <i class="fas fa-sync font-size-14"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="partner_id_operator" value="=">
                    </form>

                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                            @php
                                function sort_link($field, $title) {
                                    $currentSort = request('sort');
                                    $currentDir = request('dir', 'desc');
                                    $isActive = $currentSort === $field;
                                    $nextDir = $isActive && $currentDir === 'desc' ? 'asc' : 'desc';
                                    $icon = $isActive ? ($currentDir === 'desc' ? '↓' : '↑') : '';
                                    $query = array_merge(request()->all(), ['sort' => $field, 'dir' => $nextDir]);
                                    $url = request()->url() . '?' . http_build_query($query);
                                    return '<a href="' . $url . '" class="' . ($isActive ? 'text-primary' : 'text-reset') . '">' . $title . ' ' . $icon . '</a>';
                                }
                            @endphp
                            <tr>
                                <th class="align-middle">ID</th>
                                <th class="align-middle">Партнер</th>
                                <th class="align-middle">{!! sort_link('stat_month', 'Дата') !!}</th>
                                <th class="align-middle">{!! sort_link('total_amount', 'Общая сумма') !!}</th>
                                <th class="align-middle">{!! sort_link('total_count', 'Общее кол-во') !!}</th>
                                <th class="align-middle">{!! sort_link('sv_amount', 'Uzcard сумма') !!}</th>
                                <th class="align-middle">{!! sort_link('sv_count', 'Uzcard кол-во') !!}</th>
                                <th class="align-middle">{!! sort_link('humo_amount', 'Humo сумма') !!}</th>
                                <th class="align-middle">{!! sort_link('humo_count', 'Humo кол-во') !!}</th>
                                <th class="align-middle">{!! sort_link('cancelled_count', 'Отмененное кол-во') !!}</th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($stats as $stat)
                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">#{{$stat->partner_id}}</a> </td>
                                    <td>{{$stat->partner?->name}}</td>
                                    <td>
                                        {{$stat->stat_month}}
                                    </td>
                                    <td>
                                        {{number_format($stat->total_amount/100,2,'.')}} <br>
                                        <span class="text-{{($stat->stats['total_amount_percent'] ?? 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent($stat->stats['total_amount_percent'] ?? '')}}</span>
                                    </td>
                                    <td>
                                        {{$stat->total_count}} <br>
                                        <span class="text-{{($stat->stats['total_count_percent'] ?? 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent($stat->stats['total_count_percent'] ?? '')}}</span>
                                    </td>
                                    <td>
                                        {{number_format($stat->sv_amount/100,2,'.')}} <br>
                                        <span class="text-{{($stat->stats['sv_amount_percent'] ?? 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent($stat->stats['sv_amount_percent'] ?? '')}}</span>
                                    </td>
                                    <td>
                                        {{$stat->sv_count}} <br>
                                        <span class="text-{{($stat->stats['sv_count_percent'] ?? 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent($stat->stats['sv_count_percent'] ?? '')}}</span>
                                    </td>
                                    <td>
                                        {{number_format($stat->humo_amount/100,2,'.', ',')}}<br>
                                        <span class="text-{{($stat->stats['humo_amount_percent'] ?? 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent($stat->stats['humo_amount_percent'] ?? '')}}</span>
                                    </td>
                                    <td>
                                        {{$stat->humo_count}} <br>
                                        <span class="text-{{($stat->stats['humo_count_percent'] ?? 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent($stat->stats['humo_count_percent'] ?? '')}}</span>
                                    </td>
                                    <td>
                                        {{$stat->cancelled_count}} <br>
                                        <span class="text-{{($stat->stats['cancelled_count_percent'] ?? 0) > 0 ? 'success' : 'danger'}}">{{\App\Services\Helpers\Helper::formatPercent($stat->stats['cancelled_count_percent'] ?? '')}}</span>
                                    </td>
                                    </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $stats->withQueryString()->links() !!}
                    </div>
                    <!-- end table-responsive -->
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
