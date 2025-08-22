@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Регистрация карты</h4>
            </div>
        </div>
        <div class="col-12">
            <x-alert-success/>
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 my-5">
                    <div class="card">
                        <form action="{{ route('monitoring-requests.send') }}" method="POST">
                            @csrf
                        <div class="card-body">
                            <h4 class="card-title mb-4">Регистрация карты</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-5">
                                        <label for="card_number">Номер карты</label>
                                        <input id="input-mask" class="form-control input-mask" name="card_number"
                                               data-inputmask="'mask': '9999 9999 9999 9999'" placeholder="____ ____ ____ ____" value="{{ old('card_number', $card_number??request()->card_number) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-5">
                                        <label for="expire">Срок</label>
                                        <input id="input-date1" class="form-control input-mask"
                                               data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/yy" placeholder="mm/yy" name="expire" value="{{ old('expire', request()->expire) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Тип</label>
                                    <select name="type" class="form-select">
                                        <option value="">Все</option>
                                        <option value="humo" @if(($type ?? old('type', request()->type)) == 'humo') selected @endif>Humo</option>
                                        <option value="uzcard" @if(($type ?? old('type', request()->type)) == 'uzcard') selected @endif>Uzcard</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Телефон
                                        <sup class="text-danger">*</sup>
                                        <i data-bs-toggle="tooltip" data-bs-placement="right" class="fas fa-question-circle mx-1 text-info"
                                           title="Обязательно только для карт Humo!"></i>
                                    </label>
                                    <input type="text" class="form-control input-mask"
                                           name="phone"
                                           data-inputmask="'mask': '99-999-99-99'"
                                           im-insert="true">
                                </div>
                                <div class="text-end mt-5">
                                    <button type="submit" class="btn btn-success">Отправить</button>
                                    <a href="{{ route('monitoring-requests.index') }}" class="btn btn-secondary">Назад</a>
                                </div>
                            </div>
                        </div>
                    </form>
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
@endsection
