<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">Tra cứu Phiếu mượn sách</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?=site_url('cms')?>">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tra cứu PMS</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Tra cứu</h3>
                        </div>

                        <form action='' role="form" method="POST">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tìm theo ngày:</label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                                </div>
                                                <input type="text" class="form-control float-right" id="search_range" name="search_range">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="user_name">Tìm theo người mượn:</label>
                                            <input type="text" class="form-control" id="user_name" name="user_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-success float-right">Tìm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- /.content -->

    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3>Kết quả tìm kiếm</h3>

                <div class="card-tools">
                    <!-- pagination -->
                </div>
            </div>
            <?php if(isset($borrows) && $borrows):?>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 140px">Mã PMS</td>
                            <th style="width: 150px">Ngày tạo PMS</th>
                            <th style="width: 250px">Người mượn</th>
                            <th style="width: 150px">Ngày nhận sách</th>
                            <th style="width: 150px">Ngày phải trả sách</th>
                            <th style="width: 150px">Ngày đem trả sách</th>
                            <th style="width: 200px">Tình trạng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($borrows as $borrow):?>
                            <tr>
                                <td><?=$borrow['id']?></td>

                                <td><?=DateTime::createFromFormat('Y-m-d H:i:s', $borrow['create_date'])->format('d-m-Y')?></td>

                                <td>
                                    <a href="/cms/user/form/<?=$borrow['user_id']?>">
                                        <?=$borrow['user_name'].' (ID: '.$borrow['user_id'].')'?>
                                    </a>
                                </td>

                                <?php if ($borrow['taken_date'] != '0000-00-00 00:00:00'):?>
                                <td><?=DateTime::createFromFormat('Y-m-d H:i:s', $borrow['taken_date'])->format('d-m-Y')?></td>
                                <?php else:?>
                                <td></td>
                                <?php endif?>

                                <td><?=DateTime::createFromFormat('Y-m-d H:i:s', $borrow['return_date'])->format('d-m-Y')?></td>

                                <?php if ($borrow['brought_date'] != '0000-00-00 00:00:00'):?>
                                <td><?=DateTime::createFromFormat('Y-m-d H:i:s', $borrow['brought_date'])->format('d-m-Y')?></td>
                                <?php else:?>
                                <td></td>
                                <?php endif?>

                                <td>
                                    <?php
                                        if ($borrow['status'] == 0)
                                        {
                                            echo 'Đang mở';
                                        }
                                        elseif ($borrow['status'] == 1)
                                        {
                                            echo 'Đang mượn sách';
                                        }
                                        elseif ($borrow['status'] == 2)
                                        {
                                            echo 'Chưa trả sách';
                                        }
                                        elseif ($borrow['status'] == 3)
                                        {
                                            echo 'Đã trả sách';
                                        }
                                    ?>
                                </td>

                                <td>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="<?=site_url('cms/borrows/form/'.$borrow['id'])?>" type="button" class="btn btn-block btn-success btn-sm">Chỉnh sửa</a>
                                        </div>
                                        <div class="col-md-3">
                                        <form action="<?=site_url('cms/borrows/delete_borrows')?>" method="post" id="form_delete_borrow_<?=$borrow['id']?>">
                                            <input onclick="delete_data(this, <?=$borrow['id']?>)" name="delete" value="Xóa" type="button" class="btn btn-danger btn-sm" data-loading-text="Doing...">
                                            <input type="hidden" name="id" value="<?=$borrow['id']?>">
                                            <input type="hidden" name="url_back" value="<?=site_url('cms/borrows/search')?>">
                                        </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
            </div>
            <?php endif?>
        </div>
    </section>
</div>

<script>
    function delete_data(param, id)
    {
        var sb_form = confirm('Do you want to delete this data?');

        if (!sb_form)
        {
            return false;
        }
        else
        {
            $(param).button('loading');
            $('#form_delete_borrow_'+id).submit();
        }
    }

    $(function(){
        $('#search_range').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
    });
</script>