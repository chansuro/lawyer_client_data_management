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
                          <!-- <th>
                            SMS
                          </th> -->
                          <th>
                            Email
                          </th>
                          <th>
                            WhatsApp
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
                          <!-- <td>
                          @if($campaign->sms == 'Y')
                            <a href="http://">Send</a>
                          @endif
                          </td> -->
                          <td id="emailcampaigntd{{$campaign->id}}">
                          @if($campaign->email == 'Y')
                            <a href="javascript: void(0)" id="emailcampaign{{$campaign->id}}" class="sendemail" campaignId="{{$campaign->id}}">Send</a>
                          @elseif($campaign->email == 'Y' && $campaign->sent_on_email != null)
                          {{ \Carbon\Carbon::parse($campaign->sent_on_email)->format('d-M-Y')}}
                          @else
                          
                          @endif
                          </td>
                          <td id="wpcampaigntd{{$campaign->id}}">
                          @if($campaign->whatsapp == 'Y' && $campaign->sent_on_whatsapp == null)
                          <a href="javascript: void(0)" id="wpcampaign{{$campaign->id}}" class="sendwp" campaignId="{{$campaign->id}}">Send</a>
                          @elseif($campaign->whatsapp == 'Y' && $campaign->sent_on_whatsapp != null)
                          {{ \Carbon\Carbon::parse($campaign->sent_on_whatsapp)->format('d-M-Y')}}
                          @else
                          
                          @endif
                          </td>
                          <td>
                          {{ $campaign->created_at }}
                          </td>
                          <td>
                          <a href="{{route('customer.campaignwise',['id'=> $campaign->id ])}}">
                            <i class="ti-user"></i>
                          </a>
                          <a href="{{route('campaign.edit',['id'=>$campaign->id])}}" class="btn btn-transparent border-0 p-0 m-0">
                                                        <i class="ti-pencil"></i> 
                          </a>
                          <button type="button" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $campaign->id }}"
                          data-id="{{ $campaign->id }}" data-name="{{ $campaign->name }}" class="btn btn-transparent border-0 p-0 m-0">
                            <i class="ti-trash"></i>
                          </button>

                          <a href="{{route('customer.export', ['id'=>$campaign->id] )}}" class="btn btn-transparent border-0 p-0 m-0">
                              <i class="ti-download"></i> 
</a>
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
        <script>
        $('.sendemail').click(function () {
          var attrId = `#emailcampaign${$(this).attr('campaignId')}`;
          var emailcampaigntd = `#emailcampaigntd${$(this).attr('campaignId')}`;
          $(attrId).text('Loading ...');
            $.ajax({
                url: "{{ route('email.send') }}", // Use named route
                type: 'POST',
                data: {
                    campaignid: $(this).attr('campaignId')
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $(emailcampaigntd).html(`<p class="text-success">Email sent. [${response.sent_date}]</p>`);
                },
                error: function (xhr) {
                    $(emailcampaigntd).html('<p class="text-danger">Sending failed. Please try after sometime.</p>');
                }
            });
        });
        $('.sendwp').click(function () {
          //alert($(this).attr('campaignId'));
          var attrId = `#wpcampaign${$(this).attr('campaignId')}`;
          var wpcampaigntd = `#wpcampaigntd${$(this).attr('campaignId')}`;
          //alert(attrId)
          $(attrId).text('Loading ...');
            $.ajax({
                url: "{{ route('test.twilio') }}", // Use named route
                type: 'POST',
                data: {
                    campaignid: $(this).attr('campaignId')
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log(response);
                    $(wpcampaigntd).html(`<p class="text-success">Email sent. [${response.sent_date}]</p>`);
                },
                error: function (xhr) {
                    $(wpcampaigntd).html('<p class="text-danger">Sending failed. Please try after sometime.</p>');
                }
            });
        });
        
    </script>
      <x-adminfooter />