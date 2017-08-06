@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.partner.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Поставка от партнеров</h3>
                </div>
            </div>
            <div class="col-sm-10">
                <p>Мы сотрудничаем с несколькими крупными поставщиками аналогичной продукции в Украине. Вы можете заказать продукцию которая отсутствует у нас на складе из каталога наших партнеров.</p>
            <p>Продукция от партнеров помечена в нашем каталоге ключевым словом "предзаказ".</p>
                <p>Срок поставки заказанного товара составит 2-5 дней непосредственно до ваших дверей.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Как заказать</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <ul>
                    <li><p>Зайдите в каталог продукции и положите в корзину любой интересующий товар.</p></li>
                    <li><p>Пройдите стандартную процедуру оформления.</p></li>
                    <li><p>Выбранный товар будет автоматически рассортирован по срокам доставки.</p></li>
                    <li><p>Отслеживайте в личном кабинете статус доставки.</p></li>
                    <li><p>Отслеживайте в личном кабинете статус доставки.</p></li>
                </ul>
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