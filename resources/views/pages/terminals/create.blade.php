@extends('layouts.master')

@section('content')
    <form action="{{ route('contracts.store',$client->id) }}" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Создать нового контракта для <b class="text-danger">{{ $client->fio }}</b></h4>
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-6 form-group mb-3">
                                <label>Контракт №</label>
                                <input type="text" class="form-control" name="contract" value="{{ old('contract',$client->contract) }}">
                                @error('contract')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label>Инвестор</label>
                                <select name="investor_id" class="form-select">
                                    <option value="0">Я сам инвестор</option>
                                    @foreach($investors as $investor)
                                        <option value="{{ $investor->id }}">{{ "$investor->fio ($investor->amount $investor->currency)" }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label>Товар</label>
                                <input type="text" class="form-control" name="product" value="{{ old('product',$client->product) }}">
                                @error('product')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label>Серия номер товара</label>
                                <input type="text" class="form-control" name="product_code" value="{{ old('product_code',$client->product_code) }}">
                                @error('product_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label>Дата контракта</label>
                                <input type="date" class="form-control" name="contract_date" value="{{ old('contract_date',$client->contract_date ?? date('Y-m-d')) }}">
                                @error('contract_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label>Дата оплаты</label>
                                <input type="date" class="form-control" name="payment_date" id="payment_date" value="{{ old('payment_date',$client->payment_date ?? date('Y-m-d')) }}">
                                @error('payment_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label>Создатель</label>
                                <input class="form-control" value="{{ auth()->user()->name }}" readonly>
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label>Валюта</label>
                                <select name="currency" class="form-select">
                                    <option value="usd" @if($client->currency == 'usd') selected @endif>USD ($)</option>
                                    <option value="uzs" @if($client->currency == 'usz') selected @endif>UZS (sum)</option>
                                </select>
                            </div>
                        </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-4 form-group mb-3">
                            <label>Цена товара</label>
                            <input type="number" class="form-control" name="product_price" id="product_price" min="0" value="{{ old('product_price',$client->product_price) }}"
                                   onkeyup="calcRemaining()">
                        </div>
                        <div class="col-lg-4 form-group mb-3">
                            <label>Расход</label>
                            <input type="number" class="form-control" name="consumption" id="consumption" min="0" value="{{ old('consumption',$client->consumption) }}"
                                   onkeyup="calcRemaining()" placeholder="0">
                        </div>
                        <div class="col-lg-4 form-group mb-3">
                            <label>Первоначальный платеж</label>
                            <input type="number" class="form-control" name="initial_payment" id="initial_payment" min="0" value="{{ old('initial_payment',$client->initial_payment) }}"
                                   onkeyup="calcRemaining()" placeholder="0">
                        </div>
                        <div class="col-lg-4 form-group mb-3">
                            <label>Процент</label>
                            <input type="number" class="form-control" step="0.001" name="percentage" id="percentage" min="0" value="{{ old('percentage',$client->percentage) }}" max="100"
                                   onkeyup="calcRemaining()">
                        </div>
                        <div class="col-lg-4 form-group mb-3">
                            <label>Месяц рассрочки</label>
                            <input type="number" class="form-control" name="month" id="month" min="0" value="{{ old('month',$client->month) }}" onkeyup="calcRemaining()">
                        </div>
                        <div class="col-lg-4 form-group mb-3">
                            <label>Сумма рассрочки</label>
                            <input type="number" class="form-control" step="0.001" name="total_debit" min="0" id="total_debit" value="{{ old('total_debit',$client->total_debit) }}" placeholder="0"
                            onkeyup="calcRemaining(true)">
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
                $('#total_debit').val(Math.max(0,debit))
            }
            else
            {
                percentage = debit * 100 / amount - 100;
                $('#percentage').val(Math.max(percentage,0))
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
