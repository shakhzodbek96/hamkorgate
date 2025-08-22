@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-6 offset-lg-3 col-sm-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Редактировать Партнера</h4>

                    <form method="POST" action="{{ route('partners.update', $partner->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <label for="partner-name" class="form-label">Наименование</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="partner-name"
                                       name="name"
                                       value="{{ old('name', $partner->name) }}"
                                       placeholder="Введите наименование">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="partner-commission" class="form-label">Комиссия Uzcard</label>
                                    <input type="number"
                                           class="form-control @error('commission') is-invalid @enderror"
                                           id="partner-commission"
                                           name="commission"
                                           value="{{ old('commission', $partner->commission) }}"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           placeholder="Введите комиссию">
                                    @error('commission')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="partner-inn" class="form-label">ИНН</label>
                                    <input type="number"
                                           class="form-control @error('inn') is-invalid @enderror"
                                           id="partner-inn"
                                           name="inn"
                                           value="{{ old('inn', $partner->inn) }}"
                                           placeholder="Введите ИНН">
                                    @error('inn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="partner-phone" class="form-label">Телефон</label>
                                    <input type="text" class="form-control input-mask @error('phone') is-invalid @enderror" name="phone"
                                           data-inputmask="'mask': '99-999-99-99'" im-insert="true"
                                           value="{{ old('phone', $partner->phone) }}">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="partner-is-active" class="form-label">Статус партнера</label>
                                    <select class="form-select @error('is_active') is-invalid @enderror"
                                            name="is_active">
                                        <option value="1" {{ old('is_active', $partner->is_active) == 1 ? 'selected' : '' }}>Актив</option>
                                        <option value="0" {{ old('is_active', $partner->is_active) == 0 ? 'selected' : '' }}>Отключено</option>
                                    </select>
                                    @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="partner-auto" class="form-label">Автосписание партнера</label>
                                    <select class="form-select @error('auto') is-invalid @enderror"
                                            id="partner-auto"
                                            name="auto">
                                        <option value="1" {{ old('auto', $partner->auto) == 1 ? 'selected' : '' }}>Актив</option>
                                        <option value="0" {{ old('auto', $partner->auto) == 0 ? 'selected' : '' }}>Отключено</option>
                                    </select>
                                    @error('auto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 float-end">
                            <button type="submit" class="btn btn-outline-primary btn-rounded mx-2 w-md">
                                <i class="fa fa-save"></i> Обновить
                            </button>
                            <a href="{{ route('partners.index') }}" class="btn btn-outline-secondary btn-rounded w-md">
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
