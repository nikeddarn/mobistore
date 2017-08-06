@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.wholesale.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Оптовым покупателям</h3>
                </div>
            </div>
            <div class="col-sm-10">
                <p>По умолчанию в каталоге продукции показываются розничные цены.</p>

            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Станьте постоянным покупателем</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>Если вы являетесь сотрудником сервисного центра, оптовым покупателем или постоянным розничным покупателем, вам автоматически будут предоставлены <strong>оптовые цены</strong>.</p>
                <p>Для этого вам необходимо <a href="login">зарегистрироваться</a> , отметив в форме регистрации галочкой поле "Я оптовый покупатель". После этого вам станут доступны начальные оптовые цены.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Преимущества постоянных покупателей</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>Для оптовых и постоянных покупателей у нас действует гибкая система скидок, бесплатная доставка и толерантное гарантийное обслуживание.</p>
                <p>В зависимости от суммы текущего заказа и общего оборота покупателя вам будет предоставляться дополнительная скидка.</p>
                <p>В процессе работы с нами вам автоматически будет увеличиваться понижающий ценовой коэфициент.</p>
                <p>Мы всегда готовы обсудить с вами оптовые цены, если предложенные на сайте вас по каким-то причинам не устроили.</p>
            </div>
            <div class="col-sm-6">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">Дополнительные возможности</h4>
                </div>
            </div>
            <div class="col-sm-8">
                <p>Мы также сотрудничаем с другими поставщиками данной продукции. Вы можете <a href="/partner">заказать&nbsp;товар&nbsp;от&nbsp;партнеров</a>, который будет доставлен вам в короткие сроки.</p>
                <p>Также возможна <a href="/manufacturer">прямая&nbsp;поставка&nbsp;от&nbsp;производителя</a> на заказ. При этом мы предлагаем вам минимальную наценку на доставку. Имеется возможность дать запрос на поиск редкого и эксклюзивного товара от производителя.</p>
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