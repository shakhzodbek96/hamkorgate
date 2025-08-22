@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="text-muted text-wrap">
                                        <h6 class="mb-1">{{ $partner->name  }}</h6>
                                        <p class="mb-0">INN: {{ $partner->inn }}</p>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-muted">
                                        <p class="text-muted text-truncate mb-2">Телефон</p>
                                        <h5 class="mb-0 text-primary d-inline-flex">{{ \App\Services\Helpers\Helper::phoneShowFormatting($partner->phone) }}</h5>
                                    </div>
                                </div>
                                <div class="dropdown ms-2">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="offcanvas" data-bs-target="#config"
                                            aria-controls="offcanvasRight">
                                        <i class="fa fa-cogs font-size-16"></i> Конфигурация
                                    </button>
                                </div>
                            </div>
                            @php $config = json_decode($partner->config,1); @endphp
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="config"
                                 aria-labelledby="offcanvasRightLabel" style="visibility: hidden;"
                                 aria-hidden="true">
                                <div class="offcanvas-header">
                                    <h5 id="offcanvasRightLabel">Конфигурация для <span
                                            class="fw-bold text-primary">{{ $partner->name }}</span></h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                            aria-label="Закрыть"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="{{ route('partners.configurations',$partner->id) }}" method="post">
                                        @method('PUT')
                                        <div class="row">
                                            @csrf
                                            <h5 class="fw-bold text-primary">Аутентификация</h5>
                                            <div class="mb-3 col-lg-6 col-sm-12">
                                                <label class="form-label">Логин</label>
                                                <input type="text" class="form-control" name="config[auth][username]"
                                                       value="{{ $config['auth']['username'] }}">
                                            </div>
                                            <div class="mb-3 col-lg-6 col-sm-12">
                                                <label class="form-label">Пароль</label>
                                                <input type="password" class="form-control"
                                                       name="config[auth][password]"
                                                       value="{{ $config['auth']['password'] }}">
                                            </div>
                                            <div class="mb-3 col-lg-12 col-sm-12">
                                                <label class="form-label">Токен</label>
                                                <input type="text" class="form-control" name="config[auth][token]"
                                                       value="{{ $config['auth']['token'] }}">
                                            </div>
                                            <hr>
                                            <h5 class="pt-2 fw-bold text-primary">Вебхук</h5>
                                            <div class="mb-3 col-lg-9 col-sm-8">
                                                <label class="form-label">Хост</label>
                                                <input type="url" class="form-control" name="config[webhook][host]"
                                                       value="{{ $config['webhook']['host'] }}">
                                            </div>
                                            <div class="mb-3 col-lg-3 col-sm-4">
                                                <label class="form-label">Статус</label>
                                                <select name="config[webhook][status]" class="form-select">
                                                    <option value="1"
                                                            @if($config['webhook']['status'] == true) selected @endif>On
                                                    </option>
                                                    <option value="0"
                                                            @if($config['webhook']['status'] == false) selected @endif>
                                                        Off
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-lg-12">
                                                <label class="form-label">Токен</label>
                                                <input type="text" class="form-control" name="config[webhook][token]"
                                                       value="{{ $config['webhook']['token'] }}">
                                            </div>

                                            <hr>
                                            <h5 class="pt-2 fw-bold text-primary">СМС</h5>
                                            <div class="mb-3 col-lg-9 col-sm-8">
                                                <label class="form-label">Хост</label>
                                                <input type="url" class="form-control" name="config[sms][host]"
                                                       value="{{ $config['sms']['host'] }}">
                                            </div>
                                            <div class="mb-3 col-lg-3 col-sm-4">
                                                <label class="form-label">Статус</label>
                                                <select name="config[sms][status]" class="form-select">
                                                    <option value="true"
                                                            @if($config['sms']['status'] == true) selected @endif>On
                                                    </option>
                                                    <option value="false"
                                                            @if($config['sms']['status'] == false) selected @endif>Off
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-lg-12">
                                                <label class="form-label">Токен</label>
                                                <input type="text" class="form-control" name="config[sms][token]"
                                                       value="{{ $config['sms']['token'] }}">
                                            </div>

                                            <hr>
                                            <h5 class="pt-2 fw-bold text-primary">Уведомления в Telegram</h5>
                                            <div class="mb-3 col-lg-6">
                                                <label class="form-label">Канал платежей</label>
                                                <input type="text" class="form-control"
                                                       name="config[notifications][payment]"
                                                       value="{{ $config['notifications']['payment'] }}">
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label class="form-label">Канал предупреждений</label>
                                                <input type="text" class="form-control"
                                                       name="config[notifications][warnings]"
                                                       value="{{ $config['notifications']['warnings'] }}">
                                            </div>
                                        </div>


                                        <div class="pt-3 float-end">
                                            <button type="submit" class="btn btn-outline-success mx-2 w-md"><i
                                                    class="fa fa-save"></i> Сохранить
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary w-md"
                                                    data-bs-dismiss="offcanvas"
                                                    aria-label="Закрыть">Закрыть
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3 pe-1">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Пополнение</p>
                                        <h5 class="mb-0 text-success d-inline-flex">569,359<i
                                                class="bx bx-down-arrow-alt align-middle"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Снятие</p>
                                        <h5 class="mb-0 text-warning d-inline-flex">532,073.49<i
                                                class="bx bx-up-arrow-alt align-middle"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Расход</p>
                                        <h5 class="mb-0 text-danger d-inline-flex">355,391.19<i
                                                class="bx bx-caret-up align-middle"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Приход</p>
                                        <h5 class="mb-0 text-info d-inline-flex">460,584.77<i
                                                class="bx bx-caret-down align-middle"></i></h5>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                    <span
                                        class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                        <i class="bx bx-copy-alt"></i>
                                    </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Мерчанты</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>{{ $partner->merchants_count }}</h4>
                                <div class="d-flex">
                                    <span class="badge badge-soft-success font-size-12"> -</span>
                                    <span class="ms-2 text-truncate">test</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                    <i class="bx bx-archive-in"></i>
                                </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Клиенты</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>75,000 <i class="mdi mdi-chevron-down ms-1 text-success"></i></h4>
                                <div class="d-flex">
                                    <span class="badge badge-soft-success font-size-12">
                                        +10,000 </span>
                                    <span class="ms-2 text-truncate">За последние 30 дней</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                    <i class="bx bx-purchase-tag-alt"></i>
                                </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Контракты</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>15,444 <i class="mdi mdi-chevron-up ms-1 text-danger"></i></h4>

                                <div class="d-flex">
                                    <span class="badge badge-soft-danger font-size-12"> 84</span>
                                    <span class="ms-2 text-truncate">test</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Роли <i class="bx bxs-directions text-primary"></i></h4>
                    <div class="tab-pane active table-responsive" id="investment" role="tabpanel"
                         style="max-height: 700px;">
                        <table class="table align-middle table-nowrap w-100">
                            <thead>
                            <tr>
                                <th scope="col">Рол</th>
                                <th scope="col">Разрешение</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Admin</td>
                                <td class="text-wrap">
                                    <i style="cursor: pointer" onclick="showComment(this,11210)">Writer, Role</i>
                                    <span id="" style="display: none">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Commodi debitis eius et excepturi impedit maxime minima modi nesciunt nihil perferendis quaerat quasi quos repellat sequi sunt, tempore totam ut velit.</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>

        function showComment(obj, id) {
            $(obj).hide()
            $("#comment-" + id).show();
        }

        $('#clients_table').addClass('w-100').DataTable({
            responsive: true,
            searching: false,
            ordering: false,
            paging: false,
            info: false,
        });
    </script>
@endsection
