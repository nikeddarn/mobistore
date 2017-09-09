@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.about.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">О нашей компании</h3>
                </div>
            </div>
            <div class="col-sm-10">
                <p>Наша компания занимается продажей комплектующих к любым мобильным телефонам и планшетам.</p>
                <p>Также предлагается поставка товара под заказ по более выгодным ценам.</p>
                <p>Доступна услуга поика и доставки редких специфических запчастей под заказ.</p>
                <p>Доставка комплектующих, находящихся в наличии производится в день заказа или на следующий день. Срок поставки со склада в Украине 2-5 дней. Срок поставки под заказ от производителя 2-3 недели.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Качество товара</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>Оригинальные запчасти известных брендов (Apple, Samsung и т.д) поставляются как новые так и б/у, что
                    отображается в каталоге продукции.</p>
                <p>Запчасти к китайским мобильным устройствам и совместимые аналоги к брендовым устройствам китайского
                    производства закупаются только в максимально возможном качестве.</p>
                <p>Вся продукция закупается у надежных поставщиков проходит 100% предпродажную проверку, поэтому процент брака невелик.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Конкурентные цены</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>Прямые поставки от производителей и оперативная доставка позволяют нам поддерживать минимальные цены.</p>
                <p>Мы всегда готовы обсудить цену с оптовыми и постоянными покупателями.</p>
                <p>При оптовой поставке под заказ у вас всегда есть возможность согласовать приемлемую для себя цену.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Доставка клиенту</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>Доставка по Киеву осуществляется курьером в день заказа или на следующий день. Возможна отправка курьерской службой на ваше отделение.</p>
                <p>Доставка по Украине осуществляется курьерской службой.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Гарантии</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>В случае отказа нашего оборудования вы можете рассчитывать на быструю замену изделия или возврат средств.</p>
                <p>Узнайте подробнее <a href="/warranty">об&nbsp;условиях&nbsp;гарантийного&nbsp;обслуживания</a></p>
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