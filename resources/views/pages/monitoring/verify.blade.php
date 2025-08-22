@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Введите СМС-код</h4>
            </div>
        </div>
        <div class="col-12">
            <x-alert-success/>
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 my-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title my-4">Подтвердите!</h4>
                            <form action="{{ route('monitoring-requests.verify') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="hidden" name="ext" value="{{ $ext }}">
                                        <input type="hidden" name="type" value="{{ $type }}">
                                        <div class="form-group mb-5">
                                            <label class="form-label">Введите код из смс, полученного на телефон: {{$phoneMask}} </label>
                                            <input id="input-mask" class="form-control input-mask" name="otp_code"
                                                   data-inputmask="'mask': '999999'" placeholder="______" value="{{ old('otpotp_code_code', request()->otp_code) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 my-4">
                                        <button type="submit" class="btn btn-success"> <i class="mdi mdi-send"></i> Отправить</button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
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
