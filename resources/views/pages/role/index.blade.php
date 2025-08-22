@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('roles.index') }}" class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-3">
                                <label>Название</label>
                                <input type="text" name="name" class="form-control" value="{{ request()->name }}">
                            </div>
                            @if(\App\Services\Helpers\Check::isAdmin())
                                <div class="cols-sm-12 col-lg-3 mb-3">
                                    <label>Выберите партнера</label>
                                    <select class="form-select select-search-partner" name="partner_id">
                                        @if(request()->partner_id)
                                            <option value="{{ request()->partner_id }}" selected>
                                            {{ \App\Models\Partner::find(request()->partner_id)->name }}
                                        @endif
                                    </select>
                                </div>
                                <input type="hidden" name="partner_id_operator" value="=">
                            @endif
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    @if(\App\Services\Helpers\Check::isAdmin())
                                        <a href="{{ route('roles.index', 'partner_id=0') }}" class="btn btn-secondary btn-rounded">
                                            <i class="fas fa-user font-size-14"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('roles.index') }}" class="btn btn-warning btn-rounded">
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
                    <div class="row mb-2">
                        <div class="col-sm-12 col-lg-6">
                            Роли
                        </div>
                        @can("Создать новую роль")
                            <div class="col-sm-12 col-lg-6">
                                <div class="text-sm-end">
                                    <a href="{{ route('roles.create') }}" type="button"
                                       class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                        <i class="fa fa-plus align-middle font-size-16"></i>
                                        Создать
                                    </a>
                                </div>
                            </div>
                        @endcan
                    </div>
                    <x-alert-success />
                    <div class="table-responsive">
                        <table class="table align-middle table-check ">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">ИД</th>
                                @if(auth()->user()->is_admin)
                                    <th class="align-middle">Партнер</th>
                                @endif
                                <th class="align-middle">Называние</th>
                                <th class="align-middle">Разрешения</th>
                                <th class="text-center w-25">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <th>#{{ $role->id }}</th>
                                    @if(auth()->user()->is_admin)
                                        <td>{{$role->partner_name ?? 'Admin'}}</td>
                                    @endif
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @foreach($role->permissions as $permission)
                                            <span
                                                class="badge badge-soft-primary font-size-12">{{ $permission->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-center w-25">
                                        <form action="{{ route('roles.destroy',$role->id) }}"
                                              method="post"> @csrf @method('delete')
                                            @can('Редактировать роли')
                                                <a href="{{ route('roles.edit',$role->id) }}"
                                                   class="btn border-0 btn-outline-success mx-2 btn-rounded waves-effect waves-light">
                                                    <i class="fas fa-pencil-alt font-size-14 align-middle"></i>
                                                </a>
                                            @endcan
                                            @can('Удаление роли')
                                                <button type="button"
                                                        class="submitButtonConfirm btn border-0 btn-outline-danger btn-rounded waves-effect waves-light">
                                                    <i class="far fa-trash-alt font-size-14 align-middle"></i>
                                                </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $roles->withQueryString()->links() }}
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
