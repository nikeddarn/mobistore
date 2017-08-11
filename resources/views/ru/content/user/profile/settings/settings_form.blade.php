<!-- Settings Edit Form -->
<form id="change-password-form" role="form" method="POST" action="/user/settings">

    {{ csrf_field() }}

    <div class="col-sm-8 form-group">
        <div class="pull-right">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-long-arrow-right"></i>
                Сохранить изменения
            </button>
        </div>
    </div>

</form>
<!-- End Settings Edit Form -->
