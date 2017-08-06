@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.retail.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Розничным покупателям</h3>
                </div>
            </div>
            <div class="col-sm-10">
                <p>Мы рады сотрудничеству как с оптовыми так и с розничными покупателями. Перед совершением покупок
                    рекомендуем вам внимательно ознакомиться с <a href="/warranty">условиями&nbsp;гарантийного&nbsp;обслуживания</a>.
                </p>

            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Как совершить покупку</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <ol>
                    <li><p>Зайти в каталог продукции и положить в корзину покупаемый товар.</p></li>
                    <li><p>Перейти в корзину и проверить наименование и количество товара.</p></li>
                    <li><p>Пройти процедуру регистрации и внесения контактных данных.</p></li>
                    <li><p>Оформить доставку.</p></li>
                    <li><p>Оплата производится при получении товара.</p></li>
                </ol>
                <p>После оформления товара вы автоматически становитесь зарегистрированным пользователем и можете
                    входить в личный кабинет где можете отслеживать статус доставки.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Повторные заказы</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>При совершении повторных покупок на любую сумму вы автоматически переводитесь в разряд постоянных
                    покупателей. Вам будет предоставляться дополнительная скидка. Чтобы увидеть цены со скидкой при
                    повторной покупке, войдите на сайт через личный кабинет.</p>
            </div>
        </div>


    </div>

@endsection

@section('description')
    <meta name="description" content="{{ trans('meta.description.home') }}">
@endsection

@section('title')
    <title>{{ trans('meta.title.home') }}</title>
@endsection