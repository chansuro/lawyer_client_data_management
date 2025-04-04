<x-adminheader />
<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">List template: {{$type}}</h4>
                  <a class="btn btn-primary" style="float:right" href="{{ route('template.createtemplate',['type' => $type ]) }}">Add Template</a>
                  <!--  -->
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            Subject
                          </th>
                          <th>
                            Message
                          </th>
                          <th>
                            Created on
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                      @foreach ($templates as $template)
                        <tr>
                          <td>
                          {{ $template->subject }}
                          </td>
                          <td>
                          {{ $template->message }}
                          </td>
                          <td>
                          {{ $template->created_at }}
                          </td>
                        @endforeach
                        </tr>
                      </tbody>
                    </table>
                    
                  </div>
                  <!--  -->
                </div><!-- Pagination Links -->
                  {{ $templates->links() }}
              </div>
              
            </div>
            
          </div>
          
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        
      <x-adminfooter />