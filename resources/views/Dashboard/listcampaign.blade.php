<x-adminheader />
<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Campaigns</h4>

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
                  <a class="btn btn-primary" style="float:right" href="{{route('campaign.add')}}">Add Campaign</a>
                  <!--  -->
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            Name
                          </th>
                          <th>
                            SMS
                          </th>
                          <th>
                            Email
                          </th>
                          <th>
                            WhatsApp
                          </th>
                          <th>
                            Sent on
                          </th>
                          <th>
                            Created On
                          </th>
                          <th>
                            Action
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                      @if(count($campaigns) > 0)
                      @foreach ($campaigns as $campaign)
                        <tr>
                          <td>
                          {{ $campaign->name }}
                          </td>
                          <td>
                          {{ $campaign->sms }}
                          </td>
                          <td>
                          {{ $campaign->email }}
                          </td>
                          <td>
                          {{ $campaign->whatsapp }}
                          </td>
                          <td>
                          {{ $campaign->sent_on }}
                          </td>
                          <td>
                          {{ $campaign->created_at }}
                          </td>
                          <td>
                          <a href="{{route('customer.campaignwise',['id'=> $campaign->id ])}}">
                            <i class="ti-user"></i>
                          </a>

                          <button type="button" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $campaign->id }}"
                          data-id="{{ $campaign->id }}" data-name="{{ $campaign->name }}" class="btn btn-transparent border-0 p-0 m-0">
                            <i class="ti-trash"></i>
                          </button>

                          <button class="btn btn-transparent border-0 p-0 m-0">
                              <i class="ti-download"></i> 
                          </button>
                          </td>
                        </tr>
                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="confirmDeleteModal{{ $campaign->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="POST" id="deleteForm" action="{{route('campaign.campaigndelete',['id'=>$campaign->id])}}">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
                                        <button type="button" class="btn btn-transparent border-0 p-0 m-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti-close"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete <strong id="userName"></strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                    </div>
                                </div>
                            </form>
                          </div>
                        </div>
                        @endforeach
                        @else
                        <tr>
                          <td colspan="7" style="text-align: center;">
                            <p>No campaigns found.</p>
                          </td>
                        </tr>
                        @endif
                      </tbody>
                    </table>
                    
                  </div>
                  <!--  -->
                </div>
                <!-- Pagination Links -->
                {{ $campaigns->links() }}
              </div>
              
            </div>
            
          </div>
          
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        
      <x-adminfooter />