@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Пользователи</h4>
                <div class="page-title-right btn-group-sm">
                    @can('Создать пользователя')
                        <div class="text-sm-end">
                            <a href="{{ route('users.create') }}" type="button" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                <i class="fas fa-user-plus align-middle font-size-16"></i> Добавить пользователя</a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.index') }}" class="justify-content-end">
                        <div class="row">
                            <div class="cols-sm-12 col-lg-2">
                                <label>Имя</label>
                                <input type="text" name="name" class="form-control" value="{{ request()->name }}">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Электронная почта</label>
                                <input type="text" name="email" class="form-control" value="{{ request()->email }}">
                            </div>
                            <div class="cols-sm-12 col-lg-2">
                                <label>Телефон</label>
                                <input type="text" name="phone" class="form-control" value="{{ request()->phone }}">
                            </div>
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
                                <input type="hidden" name="partner_id_operator" value="=">
                            @endif
                            <div class="col-sm-12 col-lg-2">
                                <div class="btn-group w-100 mt-4" role="group">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search font-size-14"></i>
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-warning btn-rounded">
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
                        <table class="table align-middle table-nowrap table-check ">
                            <thead class="table-light">
                            <tr>
                                <th class="align-middle">ИД</th>
                                <th class="align-middle">Имя</th>
                                <th class="align-middle">Электронная почта</th>
                                <th class="align-middle">Телефон</th>
                                <th class="align-middle">Роли</th>
                                <th class="text-center w-25">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <h5 class="font-size-14 mb-1">{{ $loop->iteration }}</h5>
                                    </td>
                                    <td>
                                        <h5 class="font-size-14 mb-1">{{ $user->name }}</h5>
                                        <span class="badge badge-soft-primary">{{ $user->partner->name ?? 'Admin' }}</span>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{ $user->phone }}
                                    </td>
                                    <td>
                                        <div>
                                            @foreach($user->roles as $role)
                                                <a class="badge badge-soft-primary font-size-11 m-1">{{ $role->name }}</a>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center w-25">
                                        <form action="{{ route('users.destroy',$user->id) }}" method="post">
                                            <a href="{{ route('users.edit',$user->id) }}" class="btn border-0 btn-outline-success mx-2 btn-rounded waves-effect waves-light">
                                                <i class="fas fa-user-edit font-size-16 align-middle"></i>
                                            </a>
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="submitButtonConfirm btn border-0 btn-outline-danger btn-rounded waves-effect waves-light">
                                                <i class="fas fa-user-times font-size-16 align-middle"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-advanced.init.js') }}"></script>

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
