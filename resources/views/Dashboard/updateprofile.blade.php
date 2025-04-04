<x-adminheader />
<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                @if(session('success'))
                      <p class="alert alert-success">{{ session('success') }}</p>
                  @endif

                  @if ($errors->any())
                      <ul style="list-style: none;">
                          @foreach ($errors->all() as $error)
                              <li class="alert alert-danger">{{ $error }}</li>
                          @endforeach
                      </ul>
                  @endif
                  <h4 class="card-title">Update Profile</h4>
                  <p class="card-description">
                    Please update your profile details.
                  </p>
                  @if(Auth::guard('admin')->user()->avatar)
                      <img src="{{ asset('storage/' . Auth::guard('admin')->user()->avatar) }}" alt="User Avatar" width="100">
                  @endif
                  <form action="{{ route('update.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                      <input type="file" class="form-control" name="avatar" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Upload Avatar</button>
                </form>
                <hr>
                  <form class="forms-sample" method="post" action="{{ route('update.profile') }}">
                  @csrf
                    <div class="form-group">
                      <label for="exampleInputPassword1">Full Name</label>
                      <input type="text" class="form-control" name="name" value="{{ $name }}" id="exampleInputOldPassword1" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">Email</label>
                      <input type="text" class="form-control" name="email" value="{{ $email }}" id="exampleInputEmail" placeholder="Email">
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        
      <x-adminfooter />