@extends('layouts.master')

@section('content')
    <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Создать нового клиента</h4>
                        @csrf
                        <div class="row mb-3" id="addPhones">
                            <div class="col-lg-12 form-group mb-3">
                                <label for="name">ФИО</label>
                                <input type="text" class="form-control" name="fio" value="{{ old('fio') }}">
                                @error('fio')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label for="name">Пасспорт С/Н</label>
                                <input type="text" class="form-control" name="passport_id" value="{{ old('passport_id') }}">
                                @error('passport_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label for="name">Пинфл</label>
                                <input type="text" class="form-control" name="pinfl" value="{{ old('pinfl') }}">
                                @error('pinfl')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label for="name">Основной телефон</label>
                                <input id="input-mask" type="text" class="form-control input-mask" name="phone" data-inputmask="'mask': '99-999-99-99'" im-insert="true" value="{{ old('phone') }}">
                                @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label for="name">Дополнительные телефоны</label>
                                <input type="text" class="form-control input-mask" name="phone2[1]" value="{{ old('phone2[1]') }}" data-inputmask="'mask': '99-999-99-99'" im-insert="true">
                            </div>
                        </div>
                    <button type="button" class="btn btn-outline-primary btn-sm float-start" onclick="addPhone()">
                        <i class="fa fa-plus"></i> Добавить телефон
                    </button>
                    <div class="float-end">
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Назад</a>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Нужные файли</h4>
                    <div class="col-lg-12 form-group mb-3">
                        <label for="name">Файлы</label>
                        <input type="file" class="form-control" name="files[]" multiple>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection
@section('script')
    <script src="{{ asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/form-mask.init.js') }}"></script>
    <script>
        function addPhone() {
            let phone = '<div class="col-lg-6 form-group mb-3">\n' +
                '                                <label for="name">Дополнительные телефоны</label>\n' +
                '                                <input type="text" class="form-control input-mask" name="phone2[]" data-inputmask="\'mask\': \'99-999-99-99\'" im-insert="true">\n' +
                '                            </div>';
            $('#addPhones').append(phone);
            Inputmask().mask(document.querySelectorAll("input"));
        }
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
