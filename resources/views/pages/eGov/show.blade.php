@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Информация о клиенте</h4>

                {{-- Кнопка: если eGov-данные ещё не получены --}}
                <div class="page-title-right">
                    <form action="{{ route('citizen.update.data', $citizen->id) }}" method="post">
                        @csrf
                        @if(now()->format('Y-m') != $citizen->period || \App\Services\Helpers\Check::isAdmin())
                            <button type="submit"
                                    class="btn btn-rounded btn-outline-success waves-effect waves-success">
                                <i class="fas fa-landmark me-1"></i> Обновить в E-Gov <i class="fas fa-sync me-1"></i>
                            </button>
                        @endif
                    </form>
                </div>
            </div>
            <x-alert-success/>
        </div>
    </div>

    <div class="row">
        {{-- Если у клиента есть данные eGov --}}
        @if($citizen->serviceData()->exists())

            {{-- ============================ ЛЕВАЯ КОЛОНКА ============================ --}}
            <div class="col-xl-4">
                {{-- ================== service_id = 2 (паспортные данные) ================== --}}
                @foreach($citizen->serviceData as $citizenData)
                    @if($citizenData->service_id == 2 && isset($citizenData->data) && !is_null($citizenData->data))
                        {{-- Если есть данные о паспорте --}}
                        @foreach($citizenData->data as $value)
                            <!-- Карточка профиля -->
                            <div class="card overflow-hidden">
                                <div class="bg-primary bg-soft">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="text-primary p-3">
                                                <h5 class="text-primary">
                                                    Пинфл: {{ $value['current_pinpp'] ?? 'Нет в наличии' }}
                                                </h5>
                                                <p>
                                                    Паспорт C/Н: {{ $value['current_document'] ?? 'Нет в наличии' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-5 align-self-end">
                                            <img src="{{ URL::asset('/assets/images/profile-img.png') }}"
                                                 alt="profile" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="avatar-md profile-user-wid mb-4">
                                                <img src="data:image/jpeg;base64, {{ $value['photo'] ?? '' }}"
                                                     alt="photo" class="img-thumbnail rounded-circle">
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="pt-4">
                                                <h5 class="font-size-18">
                                                    {{ ucfirst(strtolower($value['surnamelat'] ?? '')) }}
                                                    {{ ucfirst(strtolower($value['namelat'] ?? '')) }}
                                                </h5>
                                                <p class="text-muted mb-1">
                                                    {{ ucfirst(strtolower($value['citizenship'] ?? '')) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card -->

                            <!-- Подробности паспорта -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        {{ $citizenData->service->name_ru }}
                                        <i class="mdi mdi-account-details-outline text-primary"></i>
                                    </h4>

                                    <div class="table-responsive mb-5">
                                        <table class="table table-nowrap mb-0">
                                            <tbody>
                                            <tr>
                                                <th scope="row">Ф.И.О :</th>
                                                <td class="text-wrap">
                                                    {{ $value['surnamelat'] ?? '—' }}
                                                    {{ $value['namelat']   ?? '—' }}
                                                    {{ $value['patronymlat'] ?? '—' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Дата рождения :</th>
                                                <td>{{ $value['birth_date'] ?? 'Нет в наличии' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Место рождения :</th>
                                                <td>{{ $value['birthplace'] ?? 'Нет в наличии' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Национальность :</th>
                                                <td>{{ $value['nationality'] ?? 'Нет в наличии' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Страна рождения :</th>
                                                <td>{{ $value['birthcountry'] ?? 'Нет в наличии' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Пол :</th>
                                                <td>
                                                    @if(isset($value['sex']))
                                                        {{ $value['sex'] == 1 ? 'Мужской' : 'Женский' }}
                                                    @else
                                                        Нет в наличии
                                                    @endif
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Документы в массиве documents --}}
                                    @if(isset($citizenData->data[0]['documents']) && is_array($citizenData->data[0]['documents']))
                                        @foreach($citizenData->data[0]['documents'] as $document)
                                            <div class="table-responsive mb-5">
                                                <table class="table table-nowrap mb-0">
                                                    <tbody>
                                                    <tr>
                                                        @switch($document['type'] ?? '')
                                                            @case('IDMS_RECV_IP_DOCUMENTS')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    Паспорт для выезда за границу
                                                                    <i class="mdi mdi-airplane text-primary"></i>
                                                                </th>
                                                                @break
                                                            @case('IDMS_RECV_CITIZ_DOCUMENTS')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    Биометрический паспорт
                                                                    <i class="mdi mdi-passport-biometric text-primary"></i>
                                                                </th>
                                                                @break
                                                            @case('IDMS_RECV_LBG_DOCUMENTS')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    Проездной документ ЛБГ
                                                                    <i class="mdi mdi-account-alert text-primary"></i>
                                                                </th>
                                                                @break
                                                            @case('IDMS_RECV_MVD_IDCARD_CITIZEN')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    ID-карта гражданина Р/У
                                                                    <i class="mdi mdi-card-account-details-outline text-primary"></i>
                                                                </th>
                                                                @break
                                                            @case('IDMS_RECV_MVD_IDCARD_FOREIGN')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    ID-карта иностранного гражданина
                                                                    <i class="mdi mdi-earth text-primary"></i>
                                                                </th>
                                                                @break
                                                            @case('IDMS_RECV_MVD_IDCARD_LBG')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    ID-карта ЛБГ
                                                                    <i class="mdi mdi-account-question text-primary"></i>
                                                                </th>
                                                                @break
                                                            @case('IDMS_RECV_MVD_IDCARD_NEWBORN')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    ID-карта новорожденного
                                                                    <i class="mdi mdi-baby-face-outline text-primary"></i>
                                                                </th>
                                                                @break
                                                            @case('IDMS_RECV_MJ_BIRTH_CERTS')
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    Свидетельства о рождении
                                                                    <i class="mdi mdi-file-certificate text-primary"></i>
                                                                </th>
                                                                @break
                                                            @default
                                                                <th scope="row" class="card-title text-sm mb-4">
                                                                    Неизвестный документ
                                                                    <i class="mdi mdi-help-circle text-primary"></i>
                                                                </th>
                                                        @endswitch
                                                    </tr>

                                                    <tr>
                                                        <th scope="row">Номер документа :</th>
                                                        <td>{{ $document['document'] ?? 'Нет в наличии' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Дата выдачи :</th>
                                                        <td>{{ $document['datebegin'] ?? 'Нет в наличии' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Дата окончания :</th>
                                                        <td>{{ $document['dateend'] ?? 'Нет в наличии' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Место выдачи :</th>
                                                        <td class="text-wrap">{{ $document['docgiveplace'] ?? 'Нет в наличии' }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <!-- end card -->
                        @endforeach
                    @endif
                @endforeach

                {{-- ================== service_id = 7 (адрес) ================== --}}
                @foreach($citizen->serviceData as $citizenData)
                    @if($citizenData->service_id == 7)

                        @if(isset($citizenData->data) && !is_null($citizenData->data))
                            <div class="card">
                                <h4 class="card-title m-3">
                                    {{ $citizenData->service->name_ru }}
                                    <i class="bx bx-map text-danger"></i>
                                </h4>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive mb-5">
                                            <table class="table table-nowrap mb-0">
                                                <tbody>
                                                <tr>
                                                    <th scope="row">Кадастр:</th>
                                                    <td>{{ $citizenData->data['Cadastre'] ?? 'Нет в наличии' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Страна :</th>
                                                    <td>{{ $citizenData->data['Country']['Value'] ?? 'Нет в наличии' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Область:</th>
                                                    <td>{{ $citizenData->data['Region']['Value'] ?? 'Нет в наличии' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Округ:</th>
                                                    <td>{{ $citizenData->data['District']['Value'] ?? 'Нет в наличии' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Адрес:</th>
                                                    <td class="text-wrap">{{ $citizenData->data['Address'] ?? 'Нет в наличии' }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Дата регистрации :</th>
                                                    <td>
                                                        @if(isset($citizenData->data['RegistrationDate']))
                                                            {{ date('Y-m-d', strtotime($citizenData->data['RegistrationDate'])) }}
                                                        @else
                                                            Нет в наличии
                                                        @endif
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title text-center text-muted">
                                        У данного клиента нет информации по этому сервису
                                    </h4>
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
            {{-- ============================ КОНЕЦ ЛЕВОЙ КОЛОНКИ ============================ --}}

            {{-- ============================ ПРАВАЯ КОЛОНКА (две части) ============================ --}}
            <div class="col-xl-8">
                <!-- ========== Блок service_id = 3 (текущее место работы) ========== -->
                <div class="card">
                    <div class="card-body">
                        @foreach($citizen->serviceData as $citizenData)
                            @if($citizenData->service_id == 3)
                                <div class="faq-box d-flex mb-5">
                                    <div class="flex-shrink-0 me-3 faq-icon">
                                        <i class="bx bx-shopping-bag font-size-20 text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title">
                                            {{ $citizenData->service->name_ru }}
                                        </h4>
                                    </div>
                                </div>

                                @if(isset($citizenData->data['positions']) && $citizenData->data['positions'] !== null)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-check myDt">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Наименование организации</th>
                                                <th>Ставка</th>
                                                <th>Код организации</th>
                                                <th>Код ПН</th>
                                                <th>ИНН организации</th>
                                                <th>Наименование подразделения</th>
                                                <th>Должность</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($citizenData->data['positions'] as $document)
                                                <tr>
                                                    <td class="text-wrap">{{ $document['org'] ?? 'Нет в наличии' }}</td>
                                                    <td>{{ $document['rate'] ?? 'Нет в наличии' }}</td>
                                                    <td>{{ $document['org_id'] ?? 'Нет в наличии' }}</td>
                                                    <td>{{ $document['kodp_pn'] ?? 'Нет в наличии' }}</td>
                                                    <td>{{ $document['org_tin'] ?? 'Нет в наличии' }}</td>
                                                    <td class="text-wrap">{{ $document['dep_name'] ?? 'Нет в наличии' }}</td>
                                                    <td class="text-wrap">{{ $document['position'] ?? 'Нет в наличии' }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <h4 class="card-title text-center text-muted">
                                        У данного клиента нет информации по этому сервису
                                    </h4>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
                <!-- ========== Конец блока service_id = 3 ========== -->

                <!-- ========== Дополнительная информация: service_id 12,16 (Водительские данные и личный транспорт) ========== -->
                <div class="card">
                    <div class="card-body">
                        <div class="faq-box d-flex mb-3">
                            <div class="flex-shrink-0 me-3 faq-icon">
                                <i class="bx bx-car font-size-20 text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="card-title">Дополнительная информация</h4>
                                <p class="card-title-desc">Водительские данные и личный транспорт</p>
                            </div>
                        </div>

                        {{-- Таб-панели для service_id = 12 и 16 --}}
                        <div class="row">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                @foreach($services->whereIn('service_id', [12,16]) as $index => $service)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $index === 5 ? 'active' : '' }}"
                                           id="tab-{{ $service->service_id }}"
                                           data-bs-toggle="tab"
                                           href="#pane-{{ $service->service_id }}"
                                           role="tab"
                                           aria-controls="pane-{{ $service->service_id }}"
                                           aria-selected="{{ $index === 5 ? 'true' : 'false' }}">
                                            @switch($service->service_id)
                                                @case(12)
                                                    <span class="d-block d-sm-none">
                                                    <i class="fas fa-file-invoice font-size-16"></i>
                                                </span>
                                                    @break
                                                @case(16)
                                                    <span class="d-block d-sm-none">
                                                    <i class="fas fa-car font-size-16"></i>
                                                </span>
                                                    @break
                                            @endswitch
                                            <span class="d-none d-sm-block">
                                            {{ $service->name_ru }}
                                        </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content p-3 text-muted">
                                @foreach($services->whereIn('service_id', [12,16]) as $index => $service)
                                    <div class="tab-pane fade {{ $index === 5 ? 'show active' : '' }}"
                                         id="pane-{{ $service->service_id }}"
                                         role="tabpanel"
                                         aria-labelledby="tab-{{ $service->service_id }}">
                                        <div class="col-md-12">
                                            <div class="m-4">
                                                @foreach($citizen->serviceData as $blockData)
                                                    @if($blockData->service_id == $service->service_id)

                                                        {{-- ==== service_id = 12 (Водительские данные) ==== --}}
                                                        @if($service->service_id == 12)
                                                            @if(isset($blockData->data['ModelDL']) && !is_null($blockData->data['ModelDL']))
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h4 class="card-title text-muted mb-3">
                                                                            Информация о ДЛ</h4>
                                                                        <p><b>Дата
                                                                                начала: </b>{{ $blockData->data['ModelDL']['pBegin'] ?? 'Нет в наличии'}}
                                                                        </p>
                                                                        <p><b>Дата
                                                                                окончания: </b>{{ $blockData->data['ModelDL']['pEnd'] ?? 'Нет в наличии'}}
                                                                        </p>
                                                                        <p>
                                                                            <b>Выдан: </b>{{ $blockData->data['ModelDL']['pIssuedBy'] ?? 'Нет в наличии' }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h4 class="card-title text-muted mb-3">
                                                                            Информация о владельце</h4>
                                                                        <p>
                                                                            <b>Серийный
                                                                                номер: </b>{{ $blockData->data['ModelPerson']['pDoc'] ?? 'Нет в наличии'}}
                                                                        </p>
                                                                        <p>
                                                                            <b>Владелец: </b>{{ $blockData->data['ModelPerson']['pOwner'] ?? 'Нет в наличии'}}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-3">
                                                                    <h4 class="card-title text-muted mb-3">Информация о
                                                                        категории</h4>
                                                                    @foreach($blockData->data['ModelDLCategory'] as $catIndex => $valueCat)
                                                                        <div class="col-md-{{ $catIndex+3 }} mb-3">
                                                                            <p><b>Серийный
                                                                                    номер: </b>{{ $blockData->data['ModelDL']['pSerialNumber'] ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Категория: </b>{{ $valueCat['pCategory'] ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Д/окончания: </b>{{ $valueCat['pEnd'] ?? 'Нет в наличии'}}
                                                                            </p>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <p class="text-center">Нет данных</p>
                                                            @endif
                                                        @endif

                                                        {{-- ==== service_id = 16 (Личный автомобиль)   ==== --}}
                                                        @if($service->service_id == 16)
                                                            @if(isset($blockData->data) && $blockData->data != [] && !is_null($blockData->data))
                                                                @foreach($blockData->data as $valueCar)
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <p>
                                                                                <b>Тип: </b>{{ $valueCar['pTexpassportSery']  ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Марка: </b>{{ $valueCar['pTexpassportNumber'] ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Модель: </b>{{ $valueCar['pModel']            ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Цвет: </b>{{ $valueCar['pVehicleColor']        ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p><b>Дата
                                                                                    регистрации: </b>{{ $valueCar['pRegistrationDate'] ?? 'Нет в наличии'}}
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <p>
                                                                                <b>Подразделение: </b>{{ $valueCar['pDivision']       ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Год: </b>{{ $valueCar['pYear']                   ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Тип: </b>{{ $valueCar['pVehicleType']             ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p><b>Тип
                                                                                    2: </b>{{ $valueCar['pBodyTypeName']          ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Кузов: </b>{{ $valueCar['pKuzov']                 ?? 'Нет в наличии'}}
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <p>
                                                                                <b>Шасси: </b>{{ $valueCar['pShassi']         ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Вес: </b>{{ $valueCar['pFullWeight']       ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p><b>Вес
                                                                                    2: </b>{{ $valueCar['pEmptyWeight']    ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Мотор: </b>{{ $valueCar['pMotor']          ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p><b>Тип
                                                                                    топлива: </b>{{ $valueCar['pFuelType']  ?? 'Нет в наличии'}}
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <p><b>Количество
                                                                                    мест: </b>{{ $valueCar['pSeats']      ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p>
                                                                                <b>Мощность: </b>{{ $valueCar['pPower']            ?? 'Нет в наличии'}}
                                                                            </p>
                                                                            <p><b>Дата
                                                                                    счета: </b>{{ $valueCar['pDateSchetSpravka'] ?? 'Нет в наличии'}}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <p class="text-center">Нет данных</p>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div> <!-- end tab-content -->
                        </div> <!-- end row -->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->

                <!-- ========== service_id = 6 (Страховка) ========== -->
                <div class="card">
                    <div class="card-body">
                        @foreach($citizen->serviceData as $citizenData)
                            @if($citizenData->service_id == 6)

                                <div class="faq-box d-flex mb-5">
                                    <div class="flex-shrink-0 me-3 faq-icon">
                                        <i class="bx bx-shield-alt font-size-20 text-secondary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title">{{ $citizenData->service->name_ru }}</h4>
                                    </div>
                                </div>

                                @if(isset($citizenData->data[0]['applicantName']) && !is_null($citizenData->data))
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <p>
                                                <b>Страховая компания:</b>
                                                {{ $citizenData->data[0]['insuranceOrgName'] ?? 'Нет наличии' }}
                                            </p>
                                            <p>
                                                <b>Полис:</b>
                                                {{ $citizenData->data[0]['policySeria'] ?? '—' }}
                                                {{ $citizenData->data[0]['policyNumber'] ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><b>Госномер
                                                    ТС:</b> {{ $citizenData->data[0]['govNumber']     ?? 'Нет наличии' }}
                                            </p>
                                            <p><b>Тип
                                                    полиса:</b> {{ $citizenData->data[0]['policyType']    ?? 'Нет наличии' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><b>Дата начала:</b>{{ $data[0]['policyStartDate'] ?? 'Нет наличии' }}</p>
                                            <p><b>Дата окончания:</b> {{ $data[0]['policyEndDate']   ?? 'Нет наличии' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><b>Модель
                                                    ТС:</b>{{ $citizenData->data[0]['vehicleModel']    ?? 'Нет наличии' }}
                                            </p>
                                            <p><b>Страховая
                                                    премия:</b> {{ number_format($citizenData->data[0]['insurancePremium']/100 ,2,'.', ',') ?? 'Нет наличии' }}
                                            </p>
                                            <p><b>Страховая
                                                    сумма:</b> {{ number_format($citizenData->data[0]['insuranceSum']/100 ,2,'.', ',') ?? 'Нет наличии' }}
                                            </p>
                                        </div>
                                    </div>

                                    <table class="table table-sm table-hover align-middle table-check myDt">
                                        <thead class="table-light">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Фамилия</th>
                                            <th>Имя</th>
                                            <th>Отчество</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($citizenData->data[0]['drivers'] as $idx => $driver)
                                            <tr class="text-center">
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $driver['firstname']  ?? 'Нет в наличии' }}</td>
                                                <td>{{ $driver['lastname']   ?? 'Нет в наличии' }}</td>
                                                <td>{{ $driver['middlename'] ?? 'Нет в наличии' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-center text-muted">
                                                У данного клиента нет информации по этому сервису
                                            </h4>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
                <!-- ========== service_id = 4 (питьевую воду) ========== -->
                <div class="card">
                    <div class="card-body ">
                        @foreach($citizen->serviceData as $citizenData)
                            @if($citizenData->service_id == 4)
                                <div class="faq-box d-flex mb-3">
                                    <div class="flex-shrink-0 me-3 faq-icon">
                                        <i class="bx bx-water font-size-20 text-opacity-50 text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title">{{ $citizenData->service->name_ru }}</h4>
                                    </div>
                                </div>
                                <h4 class="card-title text-center text-muted">
                                    Этот сервис скоро будет доступен
                                </h4>
                                <div class="row align-center justify-content-center">
                                    <div class="w-25">
                                        <img src="{{URL::asset('/assets/images/coming-soon.svg')}}" alt="coming-soon"
                                             class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <!-- ========== service_id = 13 (месте жительства) ========== -->
                <div class="card">
                    <div class="card-body ">
                        @foreach($citizen->serviceData as $citizenData)
                            @if($citizenData->service_id == 13)
                                <div class="faq-box d-flex mb-3">
                                    <div class="flex-shrink-0 me-3 faq-icon">
                                        <i class="bx bx-map-pin font-size-20 text-opacity-50 text-pink"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title">{{ $citizenData->service->name_ru }}</h4>
                                    </div>
                                </div>
                                <h4 class="card-title text-center text-muted">
                                    Этот сервис скоро будет доступен
                                </h4>
                                <div class="row align-center justify-content-center">
                                    <div class="w-25">
                                        <img src="{{URL::asset('/assets/images/coming-soon.svg')}}" alt="coming-soon"
                                             class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div> <!-- end col-xl-8 -->

            {{-- ============================ НИЖНЯЯ ЧАСТЬ: service_id = 8 ============================ --}}
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        @foreach($citizen->serviceData as $citizenData)
                            @if($citizenData->service_id == 8)

                                <div class="faq-box d-flex mb-5">
                                    <div class="flex-shrink-0 me-3 faq-icon">
                                        <i class="bx bx-history font-size-20 text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title">{{ $citizenData->service->name_ru }}</h4>
                                    </div>
                                </div>

                                @if(isset($citizenData->data) && !is_null($citizenData->data))
                                    <table id="custom_table"
                                           class="table table-sm table-hover align-middle table-check myDt">
                                        <thead class="table-light">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Дата документа</th>
                                            <th>Сумма</th>
                                            <th>Название организации</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($citizenData->data as $idx => $row)
                                            <tr class="text-center">
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $row['DOC_DATE']  ?? 'Нет в наличии' }}</td>
                                                <td>{{ $row['SUMM']      ?? 'Нет в наличии' }}</td>
                                                <td>{{ $row['ORG_NAME']  ?? 'Нет в наличии' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div id="paginationContainer" class="float-end pagination"></div>
                                @else
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-center text-muted">
                                                У данного клиента нет информации по этому сервису
                                            </h4>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

        @else
            {{-- Если у клиента совсем нет eGov-записей --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center text-muted">Данных о клиенте нет</h4>
                    </div>
                </div>
            </div>
        @endif
    </div> <!-- end row -->
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            paginateTable("#custom_table", "#paginationContainer", 10);
        });

        function paginateTable(tableSelector, paginationContainer, rowsPerPage = 10) {
            const $table = $(tableSelector);
            const $rows = $table.find("tbody tr");
            let currentPage = 1;

            function displayRowsForPage(page) {
                const startIndex = (page - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;

                $rows.each(function (index) {
                    if (index >= startIndex && index < endIndex) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function createPagination() {
                const totalRows = $rows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage);
                const $container = $(paginationContainer);

                $container.empty();

                if (totalPages <= 1) {
                    $rows.show();
                    return;
                }

                const maxVisible = 5;
                const $ul = $("<ul>").addClass("pagination pagination-rounded");

                function createPageItem(pageNum, isActive = false, isDisabled = false, text = null) {
                    const $li = $("<li>").addClass("page-item");
                    const $a = $("<a>").addClass("page-link").attr("href", "#");

                    if (text) {
                        $a.text(text);
                    } else {
                        $a.text(pageNum);
                    }

                    if (isActive) $li.addClass("active");
                    if (isDisabled) $li.addClass("disabled");

                    $a.on("click", function (e) {
                        e.preventDefault();
                        if (!isDisabled) {
                            currentPage = pageNum;
                            displayRowsForPage(currentPage);
                            createPagination();
                        }
                    });

                    $li.append($a);
                    return $li;
                }

                function addEllipsis() {
                    return createPageItem(currentPage, false, true, "…");
                }

                const $prevLi = createPageItem(currentPage - 1, false, currentPage <= 1, '<');
                $ul.append($prevLi);

                if (totalPages <= maxVisible + 2) {
                    for (let page = 1; page <= totalPages; page++) {
                        $ul.append(createPageItem(page, page === currentPage));
                    }
                } else {

                    $ul.append(createPageItem(1, currentPage === 1));

                    let startPage = currentPage - Math.floor(maxVisible / 2);
                    let endPage = currentPage + Math.floor(maxVisible / 2);

                    if (startPage < 2) {
                        startPage = 2;
                        endPage = startPage + (maxVisible - 1);
                    }
                    if (endPage >= totalPages) {
                        endPage = totalPages - 1;
                        startPage = endPage - (maxVisible - 1);
                    }

                    if (startPage > 2) {
                        $ul.append(addEllipsis());
                    }

                    for (let page = startPage; page <= endPage; page++) {
                        $ul.append(createPageItem(page, page === currentPage));
                    }

                    if (endPage < totalPages - 1) {
                        $ul.append(addEllipsis());
                    }

                    $ul.append(createPageItem(totalPages, currentPage === totalPages));
                }

                const $nextLi = createPageItem(currentPage + 1, false, currentPage >= totalPages, '>');
                $ul.append($nextLi);

                $container.append($ul);
            }

            displayRowsForPage(currentPage);

            createPagination();
        }

    </script>
@endsection
