@extends('layouts.master')

@section('content')
  <div class="row">
      <div class="col-lg-8 offset-lg-2 col-sm-12">
          <div class="card">
              <div class="card-body">
                  <h4 class="card-title  mb-3">Изменить роль</h4>
                  <form action="{{ route('roles.update',$role->id) }}" method="post">
                      @csrf @method('put')
                      <div class="row">
                          <div class="col-lg-12 col-sm-12 mb-3">
                              <input type="text" name="name" value="{{ old('name',$role->name) }}" class="form-control @error('name') is-invalid @enderror">
                              @error('name')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>
                          <div class="col-lg-12 col-sm-12 mb-3">
                              <label for="horizontal-email-input" class="col-form-label"><i>Разрешения</i></label>
                              <div class="row mx-2">
                                  @foreach($permissions as $permission)
                                      @if($loop->iteration % 2 == 0)
                                        </div>
                                      @endif
                                      @if($loop->iteration % 2 == 0 || $loop->iteration == 1)
                                          <div class="col-lg-4 col-md-4 mb-2">
                                      @endif
                                          <div class="form-check mb-1">
                                              <input name="permissions[]" @if(array_key_exists($permission->id,$available)) checked @endif class="form-check-input" value="{{$permission->id}}" type="checkbox" id="checkBoxId_{{$permission->id}}">
                                              <label class="form-check-label" for="checkBoxId_{{$permission->id}}">
                                                  {{ $permission->name }}
                                              </label>
                                          </div>
                                      @endforeach
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="text-lg-end text-sm-center">
                              <div>
                                  <button type="submit" class="btn btn-success waves-effect waves-light w-md mx-3">
                                      <i class="fas fa-save font-size-16 align-middle me-2"></i> Сохранить
                                  </button>
                                  <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary w-md">Отмена </a>
                              </div>
                          </div>
                      </div>
                  </form>
              </div>
              <!-- end card body -->
          </div>
          <!-- end card -->
      </div>
  </div>
@endsection
