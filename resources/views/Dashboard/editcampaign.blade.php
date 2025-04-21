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
                  <h4 class="card-title">Edit campaign </h4>
                  <p class="card-description">
                    Please edit campaign to send email, sms and whatsapp.
                  </p>
                  <form class="forms-sample" method="post" action="{{ route('campaign.editaction') }}" enctype="multipart/form-data">
                  @csrf
                    <div class="form-group">
                      <label for="exampleInputCampaignname">Campaign name</label>
                      <input type="text" class="form-control" name="name" value="{{$campaign->name}}" id="exampleInputCampaignname" placeholder="Campaign name">
                    </div>
                    <div class="form-check">
                    <label class="mr-3">
                      <input type="checkbox" name="email" value="Y" @if($campaign->email == 'Y') checked @endif>
                      Email
                    </label>
                    <label class="mr-3">
                      <input type="checkbox" name="sms" value="Y"  @if($campaign->sms == 'Y') checked @endif>
                      SMS
                    </label>
                    <label>
                      <input type="checkbox" name="whatsapp" value="Y"  @if($campaign->whatsapp == 'Y') checked @endif>
                      WhatsApp
                    </label>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Email Template</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="email_template_id">
                          @foreach ($emailtemplates as $emailtemplate)
                          <option value="{{$emailtemplate['id']}}" @if($campaign->email_template_id == $emailtemplate['id']) selected="selected" @endif >{{$emailtemplate['subject']}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">SMS Template</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="sms_template_id">
                          @foreach ($smstemplates as $smstemplate)
                          <option value="{{$smstemplate['id']}}" @if($campaign->sms_template_id == $smstemplate['id']) selected="selected" @endif>{{$smstemplate['subject']}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">WhatsApp Template</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="wp_template_id">
                        @foreach ($whatsapptemplates as $whatsapptemplate)
                          <option value="{{$whatsapptemplate['id']}}" @if($campaign->wp_template_id == $whatsapptemplate['id']) selected="selected" @endif>{{$whatsapptemplate['subject']}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <input type="hidden" name="id" value="{{$campaign->id}}">
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