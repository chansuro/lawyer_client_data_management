<x-adminheader />
<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">List campaign</h4>
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
                          <i class="ti-user"></i></a>
                          </td>
                        </tr>
                        @endforeach
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