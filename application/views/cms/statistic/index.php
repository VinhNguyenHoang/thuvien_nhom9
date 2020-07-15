<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Thống kê</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?=site_url('cms')?>">Trang chủ</a></li>
                <li class="breadcrumb-item active">Thống kê</li>
                </ol>
            </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3>Từ khoá được tìm kiếm gần đây</h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                <tr>
                                    <th>
                                        Từ khoá
                                    </th>

                                    <th>
                                        Số lần tìm kiếm
                                    </th>
                                </tr>
                                </thead>

                                <tbody>
                                    <?php foreach($search_keywords as $sk):?>
                                        <tr>
                                            <td><?=$sk['keyword']?></td>
                                            <td><?=$sk['count']?></td>
                                        </tr>
                                    <?php endforeach?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3>Sách mượn nhiều nhất</h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                <tr>
                                    <th>
                                        Tên sách
                                    </th>

                                    <th>
                                        Số lần mượn
                                    </th>
                                </tr>
                                </thead>

                                <tbody>
                                    <?php foreach($recent_borrowed_books as $bb):?>
                                        <tr>
                                            <td><?=$bb['name']?></td>
                                            <td><?=$bb['borrow_count']?></td>
                                        </tr>
                                    <?php endforeach?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>