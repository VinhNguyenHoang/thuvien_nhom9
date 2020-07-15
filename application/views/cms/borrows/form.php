<?php
$required	= ' <span style="color:#FF0000"><i class="fa fa-star"></i></span>'; 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Phiếu mượn sách</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?=site_url('cms')?>">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?=site_url('cms/borrows/search')?>">Quản lý PMS</a></li>
                <li class="breadcrumb-item active">Chỉnh sửa</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="content-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Thông tin PMS</h3>
                </div>
                <?=form_open('', NULL, NULL)?>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tên sách <?=$required?></label>
                        <?=form_dropdown($attributes['borrows_books'], $attributes['borrows_books']['options'], $attributes['borrows_books']['selected'], "multiple='multiple'")?>
                        <?=form_error('book_ids[]', '<div class="error text-danger">', '</div>')?>
                    </div>

                    <div class="form-group">
                        <label>Tên người mượn <?=$required?></label>
                        <?=form_dropdown($attributes['user_id'], $attributes['user_id']['options'], $attributes['user_id']['selected'])?>
                        <?=form_error('user_id', '<div class="error text-danger">', '</div>')?>
                    </div>

                    <div class="form-group">
                        <label>Ngày phải trả sách <?=$required?></label>
                        <?=form_input($attributes['return_date'])?>
                        <?=form_error('return_date', '<div class="error text-danger">', '</div>')?>
                    </div>

                    <div class="form-group">
                        <label>Ngày nhận sách</label>
                        <?=form_input($attributes['taken_date'])?>
                        <?=form_error('taken_date', '<div class="error text-danger">', '</div>')?>
                    </div>

                    <div class="form-group">
                        <label>Ngày đem trả sách</label>
                        <?=form_input($attributes['brought_date'])?>
                        <?=form_error('brought_date', '<div class="error text-danger">', '</div>')?>
                    </div>

                    <div class="form-group icheck">
                        <label>Tình trạng phiếu mượn sách</label>
                        <?php foreach($attributes['status'] as $stt):?>
                            <div class="form-check">
                                <?=form_radio($stt)?>
                                <label class="form-check-label"><?=$stt['label']?></label>
                            </div>
                        <?php endforeach?>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>
                <?=form_close()?>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#book_ids').select2({
            placeholder: 'Chọn sách',
            theme: "classic"
        });

        $('[data-mask]').inputmask();
    });
</script>