@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Сравнение ФИО клиентов</h4>
                </div>
            </div>
            <div class="col-md-6 offset-md-3">
                <div class="card shadow rounded-4">
                    <div class="card-body">
                        <h3 class="text-center mb-4">🔍 Сравнение ФИО</h3>

                        <div class="mb-3">
                            <label for="fio1" class="form-label">ФИО 1</label>
                            <input type="text" id="fio1" class="form-control" placeholder="Введите первое ФИО" required>
                        </div>

                        <div class="mb-3">
                            <label for="fio2" class="form-label">ФИО 2</label>
                            <input type="text" id="fio2" class="form-control" placeholder="Введите второе ФИО" required>
                        </div>

                        <div class="d-grid">
                            <button id="compareBtn" class="btn btn-primary btn-lg">Сравнить</button>
                        </div>

                        <div id="resultSection" class="mt-4 d-none">
                            <h5 class="text-center mb-2">Процент совпадения:</h5>
                            <div class="progress" style="height: 30px;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                     role="progressbar" style="width: 0%;" aria-valuenow="0"
                                     aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('#compareBtn').click(function () {
            let fio1 = $('#fio1').val().trim();
            let fio2 = $('#fio2').val().trim();

            if (!fio1 || !fio2) {
                return;
            }

            $.ajax({
                url: '{{ route("compare-fio.compare") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    fio1: fio1,
                    fio2: fio2
                },
                success: function (response) {
                    let percent = response.similarity;
                    $('#resultSection').removeClass('d-none');
                    animateProgressBar(percent);
                },
                error: function () {
                    alert('Ошибка при сравнении ФИО');
                }
            });
        });

        function animateProgressBar(percent) {
            let progressBar = $('#progressBar');
            progressBar.removeClass('bg-danger bg-success bg-warning');

            if (percent > 75) progressBar.addClass('bg-success');
            else if (percent > 40) progressBar.addClass('bg-warning');
            else progressBar.addClass('bg-danger');

            let current = 0;
            let interval = setInterval(function () {
                if (current >= percent) {
                    clearInterval(interval);
                } else {
                    current++;
                    progressBar.css('width', current + '%').attr('aria-valuenow', current).text(current + '%');
                }
            }, 10);
        }
    </script>
@endsection
