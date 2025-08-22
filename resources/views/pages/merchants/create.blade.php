@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-6 offset-lg-3 col-sm-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Создать мерчант</h4>
                    <form method="POST" action="{{ route('merchants.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="merchant-name" class="form-label">Называние<sup class="text-danger">*</sup></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="merchant-name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Введите наименование"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="merchant-address" class="form-label">Адрес</label>
                                <input type="text"
                                       class="form-control @error('address') is-invalid @enderror"
                                       id="merchant-address"
                                       name="address"
                                       value="{{ old('address') }}"
                                       placeholder="Введите адрес">
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr>
                            <div class="col-md-6 mb-3">
                                <label for="merchant-sv_terminal" class="form-label">SV Терминал</label>
                                <input type="text"
                                       class="form-control @error('sv_terminal') is-invalid @enderror"
                                       id="merchant-sv_terminal"
                                       name="sv_terminal"
                                       value="{{ old('sv_terminal') }}"
                                       placeholder="Введите SV терминал">
                                @error('sv_terminal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="merchant-humo_terminal" class="form-label">Humo Терминал<sup
                                        class="text-danger">*</sup></label>
                                <input type="text"
                                       class="form-control @error('humo_terminal') is-invalid @enderror"
                                       id="merchant-humo_terminal"
                                       name="humo_terminal"
                                       value="{{ old('humo_terminal') }}"
                                       placeholder="Введите Humo терминал" required>
                                @error('humo_terminal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 mb-2">
                                <i class="text-primary font-size-12">Все терминалы должны быть успешно зарегистрированы в
                                    системе перед использованием<sup class="text-danger">*</sup></i>
                            </div>
                            <hr>
                            <div class="col-md-6 mb-3">
                                <label for="merchant-phone" class="form-label">Телефон<sup
                                        class="text-danger">*</sup></label>
                                <input type="text" class="form-control input-mask @error('phone') is-invalid @enderror"
                                       name="phone"
                                       data-inputmask="'mask': '99-999-99-99'" im-insert="true"
                                       value="{{ old('phone') }}" required>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="merchant-auto" class="form-label">Автосписание</label>
                                <select class="form-select @error('auto') is-invalid @enderror" name="auto">
                                    <option value="1" {{ old('auto', 1) == 1 ? 'selected' : '' }}>Актив</option>
                                    <option value="0" {{ old('auto', 1) == 0 ? 'selected' : '' }}>Отключено</option>
                                </select>
                                @error('auto')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="merchant-limits" class="form-label">Минимальная сумма транзакции<sup class="text-danger">*</sup>
                                    <i data-bs-toggle="tooltip" data-bs-placement="right" class="fas fa-question-circle mx-1 text-info"
                                       title="Устанавливает минимальную сумму для списания. Если баланс ниже лимита, списание не производится. Если баланс и задолженность ниже лимита, то списание произойдет только при условии, что баланс больше или равен сумме долга."></i>
                                </label>
                                <input type="number" min="1"
                                       class="form-control @error('limit') is-invalid @enderror"
                                       name="limit"
                                       value="{{ old('limit',1) }}"
                                       placeholder="Введите лимиты">
                                @error('limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="strict-mode" class="form-label">
                                    Режим автосписания
                                    <i data-bs-toggle="tooltip" data-bs-placement="right" class="fas fa-question-circle mx-1 text-info"
                                       title="Выберите строгий или нестрогий режим автосписания. В строгом режиме списание происходит только при наличии полной суммы долга. В нестрогом режиме списание может быть частичным."></i>
                                </label>
                                <select class="form-select" id="strict-mode" name="is_strict">
                                    <option value="0" {{ old('is_strict', 0) == 0 ? 'selected' : '' }}>Нестрогий
                                    </option>
                                    <option value="1" {{ old('is_strict', 0) == 1 ? 'selected' : '' }}>Строгий</option>
                                </select>

                            </div>
                            @if(auth()->user()->is_admin)
                                <div class="col-md-6 mb-3">
                                    <label for="merchant-sv_commission" class="form-label">Комиссия SV</label>
                                    <input type="text"
                                           class="form-control numberFormat @error('sv_commission') is-invalid @enderror"
                                           id="merchant-sv_commission"
                                           name="sv_commission"
                                           value="{{ old('sv_commission', 0) }}"
                                           placeholder="Введите комиссию">
                                    @error('sv_commission')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="merchant-humo_commission" class="form-label">Комиссия Humo</label>
                                    <input type="text"
                                           class="form-control numberFormat @error('humo_commission') is-invalid @enderror"
                                           id="merchant-humo_commission"
                                           name="humo_commission"
                                           value="{{ old('humo_commission', 0) }}"
                                           placeholder="Введите комиссию">
                                    @error('humo_commission')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 float-end">
                            <button type="submit" class="btn btn-outline-success btn-rounded mx-2 w-md">
                                <i class="fa fa-save"></i> Создать
                            </button>
                            <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary btn-rounded w-md">
                                Назад
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/form-mask.init.js') }}"></script>
@endsection
