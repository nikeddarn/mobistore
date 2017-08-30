@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <form method="post" action="/setup/categories/create">
                    {{ csrf_field() }}
                    <h4 class="alert alert-danger">Внимание! Все данные о товарах и категориях будут уничтожены.
                        Продолжить?</h4>
                    <div class="form-group">
                        <label for="create_categories_confirmation">Создать категории</label>
                        <input id="create_categories_confirmation" type="checkbox" name="create_categories">
                    </div>
                    <div class="form-group">
                        <button type="submit">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection