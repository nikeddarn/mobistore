@extends('layouts/common')

@section('content')

    <!-- Store Navbar -->
    @include('menu.store_navbar')

    <!-- Breadcrumbs -->
    @include('content.warranty.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Возврат и гарантийное обслуживание</h3>
                </div>
            </div>
            <div class="col-sm-10">
                <p>При возникновении гарантийной ситуации покупатель имеет право обменять товар на
                    аналогичный либо вернуть деньги в размере стоимости товара на момент покупки.</p>
                <p>Обмен товара с дефектом
                    или товара, качество которого не соответствует заявленным характеристикам производится в течение 7
                    дней
                    со дня получения нами данного товара.</p>
                <p>Если гарантийные условия нарушены - замена и возврат денег не производится. Данный товар возвращается
                    покупателю за его счет.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Условия гарантийного обслуживания</h4>
                </div>
                <div class="panel panel-danger">
                    <div class="panel-body bg-danger">
                        <p>Уважаемые покупатели.</p>
                        <p><strong>Убедительная просьба не пытаться устанавливать экраны и тачскрины самостоятельно!</strong></p>
                        <p>При чрезмерном механическом воздействии контроллер экрана разрушается. Оборудование с такими дефектами по гарантии не обслуживается.</p>
                        <p>Пожалуйста, не пытайтесь сэкономить. Пользуйтесь услугами сервисных центров!</p>
                        <p><strong>Не снимайте защитные пленки до полной проверки экрана во всех режимах</strong></p>
                        <p>Экраны и тачскрины со снятыми пленками в гарантию не принимаются.</p>
                    </div>
                </div>
                <div class="panel panel-default">
                    <p class="panel-heading text-center text-gray"><strong>Товар принимается на гарантийное обслуживание
                            при соблюдении следующих условий:</strong></p>
                    <ul>
                        <li><p>Отсутствуют механические повреждения</p></li>
                        <li><p>Защитные пленки не снимались</p></li>
                        <li><p>Отсутствуют следы эксплуатации</p></li>
                    </ul>
                </div>
                <div class="panel panel-default">
                    <p class="panel-heading text-center text-gray"><strong>Гарантия не распространяется на следующие
                            комплектующие по причине невозможности установления их происхождения:</strong></p>
                    <ul>
                        <li><p>Микросхемы</p></li>
                        <li><p>Микрофоны</p></li>
                        <li><p>Разъемы</p></li>
                        <li><p>Динамики</p></li>
                        <li><p>Коннекторы</p></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Сроки гарантии</h4>
                </div>
                <ul>
                    <li><p>Гарантийный срок на комплектующие к мобильным телефонам и планшетам - <strong>30
                                дней</strong></p></li>
                    <li><p>Для оптовых покупателей действует <strong>продленная гарантия</strong> по договоренности.</p>
                    </li>
                    <li><p>Обмен или возврат денег осуществляется в течение <strong>7 дней</strong> с момента возврата
                            товара.</p></li>
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