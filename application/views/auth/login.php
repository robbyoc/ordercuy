<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-7 d-none d-lg-block">
                            <img src="<?= base_url('assets/img/login.jpg') ?>" alt="Login Image" class="img-fluid">
                        </div>
                        <div class="col-lg-5">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900"><strong>Halaman Login</strong></h1>
                                </div>
                                <div class="text-center">
                                    <p class="small text-gray-900 mb-4">Masukan sesuai dengan akun yang anda daftarkan</p>
                                </div>
                                <hr>

                                <?= $this->session->flashdata('message'); ?>

                                <?= form_open('auth', ['class' => 'user']); ?>
                                <?= form_input('email', set_value('email'), 'class="form-control form-control-user" id="email" placeholder="Alamat Email"'); ?>
                                <?= form_error('email', '<small class="text-danger pl-3">', '</small>'); ?>
                                <br>
                                <?= form_password('password', '', 'class="form-control form-control-user" id="password" placeholder="Kata Sandi"'); ?>
                                <?= form_error('password', '<small class="text-danger pl-3">', '</small>'); ?>
                                <hr>
                                <?= form_submit('submit', 'Login', 'class="btn btn-dark btn-user btn-block"'); ?>
                                <?= form_close(); ?>

                                <hr>
                                <div class="text-center">
                                    <a class="small" href="<?= base_url('register'); ?>">Buat Akun</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>