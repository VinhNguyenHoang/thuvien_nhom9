<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/cms" class="brand-link">
      <span class="brand-text font-weight-light ml-3">CMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?=base_url()?>dist/img/avatar.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Admin</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <li class="nav-item has-treeview <?=($slug_1 == 'book')?'menu-open':''?>">
            <a class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Quản lý sách
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?=site_url('cms/book/search')?>" class="nav-link <?=($slug_1 == 'book' && $slug_2 == 'search')?'active':''?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tra cứu sách</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?=site_url('cms/book/form')?>" class="nav-link <?=($slug_1 == 'book' && $slug_2 == 'form')?'active':''?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tạo mới sách</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item has-treeview <?=($slug_1 == 'user')?'menu-open':''?>">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fas fa-users"></i>
              <p>
                Quản lý thành viên
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?=site_url('cms/user/search')?>" class="nav-link <?=($slug_1 == 'user' && $slug_2 == 'search')?'active':''?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tra cứu thành viên</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?=site_url('cms/user/form')?>" class="nav-link <?=($slug_1 == 'user' && $slug_2 == 'form')?'active':''?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tạo mới thành viên</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item has-treeview <?=($slug_1 == 'borrows')?'menu-open':''?>">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Quản lý PMS
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?=site_url('cms/borrows/search')?>" class="nav-link <?=($slug_1 == 'borrows' && $slug_2 == 'search')?'active':''?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tra cứu PMS</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?=site_url('cms/borrows/form')?>" class="nav-link <?=($slug_1 == 'borrows' && $slug_2 == 'form')?'active':''?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tạo mới PMS</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>