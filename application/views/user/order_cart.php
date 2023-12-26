<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Ordercuy - Keranjang</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/'); ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/'); ?>css/sb-admin-2.min.css" rel="stylesheet">

    <link rel="icon" href="<?= base_url('assets/') ?>img/favicon.ico">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('userHome'); ?>">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-fish"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Ordercuy</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Beranda -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('userHome'); ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Beranda</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Opsi
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item active">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Pesan</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilihan</h6>
                        <a class="collapse-item" href="<?= base_url('userOrder') ?>"><strong>Menu</strong></a>
                        <a class="collapse-item" href="<?= base_url('userHistory') ?>">Riwayat</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Akun -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('userProfile') ?>">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Akun</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Cart -->
                        <li class="nav-item no-arrow mx-1">
                            <a class="nav-link" href="<?= base_url('userCart') ?>">
                                <i class="fas fa-fw fa-shopping-cart"></i>
                                <span class="badge badge-danger badge-counter"><?php echo $this->cart->total_items(); ?></span>
                            </a>
                        </li>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= isset($user['user_fullname']) ? $user['user_fullname'] : '' ?></span>
                                <img class="img-profile rounded-circle" src="<?= base_url('assets/') ?>img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('userProfile') ?>">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <a class="dropdown-item" href="<?= base_url('userEdit') ?>">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Pengaturan
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Keranjang</h1>
                    </div>

                    <div class="card shadow mb-4 w-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Detail Keranjang</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="table-responsive d-inline-block">
                                <table class="table table-borderless" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Gambar Menu</th>
                                            <th>Nama Menu</th>
                                            <th class="col-sm-2">Jumlah</th>
                                            <th>Harga Satuan</th>
                                            <th>Harga Subtotal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_cart as $item) : ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    $image_path = base_url('assets/img/makanan/' . $item['name']);
                                                    $image_extensions = ['.jpg', '.jpeg', '.png', '.gif'];

                                                    foreach ($image_extensions as $extension) {
                                                        $image_url = $image_path . $extension;
                                                        if (file_exists(FCPATH . 'assets/img/makanan/' . $item['name'] . $extension)) {
                                                            echo '<img class="img-thumbnail" src="' . $image_url . '" alt="' . ucwords(str_replace('_', ' ', $item['name'])) . '" width="150">';
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= ucwords(str_replace('_', ' ', $item['name'])) ?></td>
                                                <td>
                                                    <div class="input-group">
                                                        <a href="<?= base_url('decreaseQuantity/' . $item['rowid']); ?>" class="btn btn-info btn-circle">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </a>
                                                        <input type="number" class="form-control-plaintext text-center" name="quantity[<?= $item['rowid']; ?>]" value="<?= $item['qty']; ?>" readonly>
                                                        <a href="<?= base_url('increaseQuantity/' . $item['rowid']); ?>" class="btn btn-info btn-circle">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>Rp <?= number_format($item['price'], 0, ',', '.'); ?></td>
                                                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <a href="<?= base_url('removeFromCart/' . $item['rowid'] . '/' . urlencode($item['name'])) ?>" class="btn btn-danger btn-circle">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="5" class="text-center"><strong>Total Harga</strong></td>
                                            <td>Rp <?= number_format($this->cart->total(), 0, ',', '.'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <form action="<?= base_url('submitOrder') ?>" method="post">
                                    <!-- disable tombol pesan jika item kosong -->
                                    <?php if (empty($order_cart)) : ?>
                                        <button type="submit" class="btn btn-success btn-icon-split" disabled>
                                            <span class="icon text-white-50">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span class="text">Pesan</span>
                                        </button>
                                        <div class="alert alert-danger mt-3" role="alert">
                                            Anda belum memasukkan pesanan. Silakan tambahkan menu ke dalam keranjang terlebih dahulu.
                                        </div>
                                    <?php else : ?>
                                        <button type="submit" class="btn btn-success btn-icon-split">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span class="text">Pesan</span>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                    </div>
                    <!-- /.container-fluid -->

                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>&copy; 2023 Ordercuy</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <?php $this->load->view('templates/logout_modal'); ?>

        <!-- Bootstrap core JavaScript-->
        <script src="<?= base_url('assets/'); ?>vendor/jquery/jquery.min.js"></script>
        <script src="<?= base_url('assets/'); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="<?= base_url('assets/'); ?>vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="<?= base_url('assets/'); ?>js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="<?= base_url('assets/'); ?>vendor/chart.js/Chart.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="<?= base_url('assets/'); ?>js/demo/chart-area-demo.js"></script>
        <script src="<?= base_url('assets/'); ?>js/demo/chart-pie-demo.js"></script>

</body>

</html>