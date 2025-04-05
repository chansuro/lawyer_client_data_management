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
                  <h4 class="card-title">Add campaign </h4>
                  <p class="card-description">
                    Please add campaign to send email, sms and whatsapp.
                  </p>
                  <form class="forms-sample" method="post" action="{{ route('campaign.create') }}" enctype="multipart/form-data">
                  @csrf
                    <div class="form-group">
                      <label for="exampleInputCampaignname">Campaign name</label>
                      <input type="text" class="form-control" name="name" id="exampleInputCampaignname" placeholder="Campaign name">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputConfirmPassword1">Upload excel data</label>
                      <input type="file" class="form-control" name="uploadfile" accept="csv*">
                    </div>
                    <div class="form-check">
                    <label class="mr-3">
                      <input type="checkbox" name="email" value="Y" checked>
                      Email
                    </label>
                    <label class="mr-3">
                      <input type="checkbox" name="sms" value="Y" checked>
                      SMS
                    </label>
                    <label>
                      <input type="checkbox" name="whatsapp" value="Y" checked>
                      WhatsApp
                    </label>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <a href="{{ route('campaign.get') }}" class="btn btn-light">Back</a>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
      <x-adminfooter />