<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">Tra cứu sách</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?=site_url('cms')?>">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tra cứu sách</li>
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
                                            <label for="book_name">Tên sách</label>
                                            <input type="text" class="form-control" id="book_name" name="book_name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="author_name">Tên tác giả</label>
                                            <input type="text" class="form-control" id="author_name" name="author_name">
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
            <?php if(isset($books) && $books):?>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 140px">Mã sách</td>
                            <th>Tên sách</th>
                            <th>Tác giả</th>
                            <th>Năm xuất bản</th>
                            <th style="width: 200px">Ngày tạo</th>
                            <th style="width: 200px">Ngày chỉnh sửa</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($books as $book):?>
                            <tr>
                                <td><?=$book['id']?></td>
                                <td><a href="<?=site_url('cms/book/form/'.$book['id'])?>"><?=$book['name']?></a></td>
                                <td><?=$book['author']?></td>
                                <td><?=$book['publish_year']?></td>
                                <td><?=$book['create_date']?></td>
                                <td><?=$book['update_date']?></td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="<?=site_url('cms/book/form/'.$book['id'])?>" type="button" class="btn btn-block btn-success btn-sm">Edit</a>
                                        </div>
                                        <div class="col-md-3">
                                        <form action="<?=site_url('cms/book/delete_book')?>" method="post" id="form_delete_book_<?=$book['id']?>">
                                            <input onclick="delete_data(this, <?=$book['id']?>)" name="delete" value="Delete" type="button" class="btn btn-danger btn-sm" data-loading-text="Đang xử lý...">
                                            <input type="hidden" name="id" value="<?=$book['id']?>">
                                            <input type="hidden" name="url_back" value="<?=site_url('cms/book/search?book_name='.$book_name.'&author_name='.$author_name.'&offset='.$offset)?>">
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
            $('#form_delete_book_'+id).submit();
        }
    }
</script>