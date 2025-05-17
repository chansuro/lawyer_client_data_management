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
                  <h4 class="card-title">Edit template: {{$type}}</h4>
                  <p class="card-description">
                    Please add {{$type}} template.
                  </p>
                  <form class="forms-sample" method="post" action="{{ route('template.edittemplateaction',['type'=>$type,'id'=>$id]) }}">
                  @csrf
                  <input type="hidden" name="type" value="{{ $type }}">
                  <input type="hidden" name="id" value="{{ $id }}">
                    <div class="form-group">
                      <label for="exampleInputPassword1">Subject</label>
                      <input type="text" class="form-control" name="subject" value="{{$template->subject}}" id="exampleInputSubject" placeholder="Subject">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputConfirmPassword1">Message</label>
                      <textarea name="message" id="exampleInputMessage" class="form-control">{{$template->message}}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <a href="{{ route('template.get',['type'=>$type]) }}" class="btn btn-light">Back</a>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
            <h4 class="card-title">Template Variables: {{$type}}</h4>
            <ul style="list-style: none;">
                          @foreach ($wildcards as $wildcard)
                              <li>{{ $wildcard }}</li>
                          @endforeach
                      </ul>
            </div>
            </div>
            </div>


          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        @if ($type == 'email') 
        <script src="https://cdn.ckeditor.com/4.18.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('exampleInputMessage');
        </script>
        @endif
      <x-adminfooter />