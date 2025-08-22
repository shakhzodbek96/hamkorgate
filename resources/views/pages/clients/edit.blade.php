@extends('layouts.master')

@section('content')
    <form action="{{ route('clients.update',$client->id) }}" method="POST">
        @method('put') @csrf
        <div class="row">
            <div class="col-md-12 col-lg-10 offset-lg-1 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Изменить клиента</h4>
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">ФИО</label>
                                <input type="text" class="form-control" name="fio" value="{{ old('fio',$client->fio) }}">
                                @error('fio')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Пасспорт С/Н</label>
                                <input type="text" class="form-control" name="passport_id" value="{{ old('passport_id',$client->passport_id) }}">
                                @error('passport_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Пинфл</label>
                                <input type="text" class="form-control" name="pinfl" value="{{ old('pinfl',$client->pinfl) }}">
                                @error('pinfl')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Основной телефон</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone',$client->phone) }}">
                                @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Дополнительные телефоны</label>
                                <input type="text" class="form-control" name="phone2" value="{{ old('phone2',$client->phone2) }}">
                                @error('phone2')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Дата контракт</label>
                                <input type="date" class="form-control" name="contract_date" value="{{ old('contract_date',$client->contract_date) }}">
                                @error('contract_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Контракт</label>
                                <input type="text" class="form-control" value="{{ $client->contract }}" readonly>
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Продукт</label>
                                <input type="text" class="form-control" name="product" value="{{ old('product',$client->product) }}">
                                @error('product')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label for="name">Продукт С/Н</label>
                                <input type="text" class="form-control" name="product_code" value="{{ old('product_code',$client->product_code) }}">
                                @error('product_code')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="float-end">
                            <button type="submit" class="btn btn-success">Сохранить</button>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Назад</a>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        </div>
    </form>
    @if(!is_null($client->contract) && $client->payments_count == 0)
        <form action="{{ route('contracts.update',$client->id) }}" method="POST">
            @csrf
        <div class="row">
            <div class="col-md-12 col-lg-10 offset-lg-1 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Изменить контракта для <b class="text-danger">{{ $client->fio }}</b></h4>
                        <div class="row mb-3">
                            <div class="col-lg-4 form-group mb-3">
                                <label>Цена товара</label>
                                <input type="number" class="form-control" name="product_price" id="product_price" min="0" value="{{ old('product_price',$client->product_price) }}"
                                       onkeyup="calcRemaining()" required>
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label>Первоначальный платеж</label>
                                <input type="number" class="form-control" step="0.001" name="initial_payment" id="initial_payment" min="0" value="{{ old('initial_payment',$client->initial_payment) }}"
                                       onkeyup="calcRemaining()"  required>
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label>Расход</label>
                                <input type="number" class="form-control" name="consumption" id="consumption" min="0" value="{{ old('consumption',$client->consumption) }}"
                                       onkeyup="calcRemaining()"  required>
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label>Процент</label>
                                <input type="number" class="form-control" step="0.0000000000000000001" name="percentage" id="percentage" min="0" value="{{ old('percentage',$client->percentage) }}" max="100"
                                       onkeyup="calcRemaining()" required>
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label>Месяц рассрочки</label>
                                <input type="number" class="form-control" name="month"
                                       id="month" min="1" value="{{ old('month',$client->month) }}" onkeyup="calcRemaining()"  required>
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label>Сумма рассрочки</label>
                                <input type="number" class="form-control" step="0.0000000000000000001" name="total_debit" min="0" id="total_debit" value="{{ old('total_debit',$client->total_debit) }}"
                                       onkeyup="calcRemaining(true)" required>
                            </div>
                            <div class="col-lg-4 form-group mb-3">
                                <label>Дата оплаты</label>
                                <input type="date" class="form-control" name="payment_date" id="payment_date" value="{{ old('payment_date',$client->payment_date ?? date('Y-m-d')) }}" min="{{ date("d-m-Y",strtotime('-1 year')) }}" max="{{ date("d-m-Y",strtotime('+ 6 months')) }}" required>
                                @error('payment_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary float-start" id="graphic_button" type="button" onclick="graphic()">
                                <i class="fa fa-table"></i> График
                            </button>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary float-end">Назад</a>
                            <button type="submit" class="btn btn-success float-end mx-3">Сохранить</button>
                        </div>
                    </div>
                </div>
                <div class="card" >
                    <div class="card-body">
                        <h4 class="card-title mb-3">Предварительный график погашения </h4>
                        <div class="table-responsive">
                            <table class="table table-sm m-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Месяц</th>
                                    <th>Дата оплаты</th>
                                    <th>Сумма</th>
                                </tr>
                                </thead>
                                <tbody id="graphic_table">
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            </div>
    </form>
    @endif
@endsection
@section('script')
    <script>
        function calcRemaining(is_debit = false) {
            let product_price = $('#product_price').val()*1;
            let debit = $('#total_debit').val()*1;
            let percentage = $('#percentage').val()*1;
            let initial_payment = $('#initial_payment').val()*1;
            let consumption = $('#consumption').val()*1;
            let amount = product_price + consumption - initial_payment;
            if (!is_debit)
            {
                debit = amount * (1 + percentage/100);
                $('#total_debit').val(Math.round(Math.max(0,debit)))
            }
            else
            {
                percentage = debit * 100 / amount - 100;
                $('#percentage').val(Math.round(Math.max(percentage,0)))
            }
            $("#graphic_table").empty()
        }

        function graphic(){

            let month = $("#month").val() * 1;
            let date_payment = $("#payment_date").val();
            let amount = $("#total_debit").val()*1;
            if (month !== 0 && date_payment !== '' && amount >= 0){
                calculatePaymentDates(month,amount,date_payment)
            }
            else {
                show("Нет данных для графика")
            }
        }

        function calculatePaymentDates(months, amount, date) {
            let table = $("#graphic_table")
            let button = $("#graphic_button");
            table.empty();
            $.ajax({
                url: '{{ route('get.graphic') }}',
                type: "post", //send it through post method
                data: {
                    _token: "{!! csrf_token() !!}",
                    months: months,
                    amount: amount,
                    date: date,
                },
                beforeSend:function () {
                    button.prop('disabled',true);
                    button.html('<i class="spinner-border spinner-border-sm text-light"></i>loading...');
                },
                success:function (result) {

                    if(result.status === true)
                    {
                        let graphics = result.result
                        graphics.forEach(function (graphic) {
                            table.append('<tr> ' +
                                '<td>'+graphic.id+'</td> ' +
                                '<td>'+graphic.month+'</td> ' +
                                '<td>'+graphic.date+'</td> '+
                                '<td class="fw-bold text-success">'+(new Intl.NumberFormat('ru-RU').format(graphic.amount))+'</td> ');
                        })
                    }
                    else{
                        show(result.error.message)
                    }

                    if(result.error)
                    {
                        show(result.error.message)
                    }
                    button.prop('disabled',false);
                    button.html('<i class="fa fa-table"></i> График');
                },
                error:function (err) {
                    console.log(err);
                    button.prop('disabled',false);
                    button.html('<i class="fa fa-table"></i> График');
                }
            });
        }

    </script>
@endsection
