<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Skydash Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="/Dashboard/vendors/feather/feather.css">
  <link rel="stylesheet" href="/Dashboard/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="/Dashboard/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="/Dashboard/css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="/Dashboard/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="/Dashboard/images/logo.svg" alt="logo">
              </div>
              @if (session('status'))
                <div class="alert alert-success"> {{ session('status') }}</div>
              @endif
              <h4>{{ __('Reset Password') }}</h4>
              <h6 class="font-weight-light">Please enter valid details to reset password</h6>
              <form class="pt-3" method="post" action="{{ route('password.update') }}">
              @csrf
              <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"  value="{{ old('email') }}" id="exampleInputEmail1" placeholder="Email / Username" required autocomplete="email" autofocus>
                  @error('email')
                      <p class="invalid-feedback">{{ $message }}</p>
                  @enderror
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" id="password" placeholder="Password" required autocomplete="new-password">
                  @error('password')
                      <p class="invalid-feedback">{{ $message }}</p>
                  @enderror
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="password_confirmation" id="password-confirm" placeholder="Password" required autocomplete="new-password">
                </div>
                <div class="mt-3">
                  <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" type="submit">{{ __('Reset Password') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="/Dashboard/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="/Dashboard/js/off-canvas.js"></script>
  <script src="/Dashboard/js/hoverable-collapse.js"></script>
  <script src="/Dashboard/js/template.js"></script>
  <script src="/Dashboard/js/settings.js"></script>
  <script src="/Dashboard/js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>
