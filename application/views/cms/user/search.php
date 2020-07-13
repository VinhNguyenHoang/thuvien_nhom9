<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">Tra cứu thành viên</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?=site_url('cms')?>">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tra cứu thành viên</li>
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

                        <form action='' role="form" method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Tên thành viên</label>
                                            <input type="text" class="form-control" id="name" name="name">
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
            <?php if(isset($users) && $users):?>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 140px">Mã thành viên</td>
                            <th>Họ tên</th>
                            <th>Ngày sinh</th>
                            <th style="width: 200px">Ngày tham gia</th>
                            <th style="width: 200px">Tình trạng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user):?>
                            <tr>
                                <td><?=$user['id']?></td>
                                <td><a href="<?=site_url('cms/user/form/'.$user['id'])?>"><?=$user['name']?></a></td>
                                <td><?=$user['birthday']?></td>
                                <td><?=$user['create_date']?></td>
                                <td>
                                    <?php
                                        if ($user['status'] == 1)
                                        {
                                            echo 'Đã kích hoạt';
                                        }
                                        elseif ($user['status'] == 0)
                                        {
                                            echo 'Chưa kích hoạt';
                                        }
                                        elseif ($user['status'] == 2)
                                        {
                                            echo 'Bị cảnh cáo';
                                        }
                                        elseif ($user['status'] == 3)
                                        {
                                            echo 'Bị cấm';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="<?=site_url('cms/user/form/'.$user['id'])?>" type="button" class="btn btn-block btn-success btn-sm">Chỉnh sửa</a>
                                        </div>
                                        <div class="col-md-3">
                                        <form action="<?=site_url('cms/user/delete_user')?>" method="post" id="form_delete_user_<?=$user['id']?>">
                                            <input onclick="delete_data(this, <?=$user['id']?>)" name="delete" value="Xóa" type="button" class="btn btn-danger btn-sm" data-loading-text="Doing...">
                                            <input type="hidden" name="id" value="<?=$user['id']?>">
                                            <input type="hidden" name="url_back" value="<?=site_url('cms/user/search?name='.$name)?>">
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
        var sb_form = confirm('Bạn xác nhận xóa dữ liệu này?');

        if (!sb_form)
        {
            return false;
        }
        else
        {
            $(param).button('loading');
            $('#form_delete_user_'+id).submit();
        }
    }
</script>