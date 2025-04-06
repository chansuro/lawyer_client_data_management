<x-adminheader />
<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Customers </h4>
                  <!--  -->
                  <form class="forms-sample" method="post" action="{{ route('customer.search') }}" enctype="multipart/form-data">
                  @csrf
                    <ul class="navbar-nav mr-lg-2">
                      <li class="nav-item nav-search d-none d-lg-block">
                        <div class="input-group">
                          <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                            <span class="input-group-text" id="search">
                              <i class="icon-search"></i>
                            </span>
                          </div>
                          <input type="text" class="form-control" id="navbar-search-input" value="{{ request('search') }}" name="search" placeholder="Search now" aria-label="search" aria-describedby="search">
                          <input type="submit" value="Search" class="btn btn-info ml-2">
                        </div>
                      </li>
                    </ul>
                  </form>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            Name
                          </th>
                          <th>
                            Email
                          </th>
                          <th>
                            Ref No
                          </th>
                          <th>
                            Card No
                          </th>
                          <th>
                            AAN No
                          </th>
                          <th>
                            Account No
                          </th>
                          <th>
                            Action
                          </th>

                        </tr>
                      </thead>
                      <tbody>
                      @foreach ($customers as $customer)
                        <tr>
                          <td>
                          {{ $customer->name }}
                          </td>
                          <td>
                          {{ $customer->email }}
                          </td>
                          <td>
                          {{ $customer->ref_no }}
                          </td>
                          <td>
                          {{ $customer->card_no }}
                          </td>
                          <td>
                          {{ $customer->aan_no }}
                          </td>
                          <td>
                          {{ $customer->account_no }}
                          </td>
                          <td>
                          <button type="button" data-bs-toggle="modal" data-bs-target="#userModal{{ $customer->id }}" class="btn btn-transparent border-0 p-0 m-0" style="background: none;">
                          <i class="ti-user"></i>
                          </button>

                          
                          </td>
                        </tr>
                        <div class="modal fade" id="userModal{{ $customer->id }}" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="userModalLabel">Customer: {{ $customer->name }}</h5>
                                <button type="button" class="btn btn-transparent border-0 p-0 m-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti-close"></i></button>
                              </div>
                              <div class="modal-body">
                              <div class="row">
                                <div class="col-md-3">Name</div>
                                <div class="col-md-9">{{ $customer->name }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Email</div>
                                <div class="col-md-9">{{ $customer->email }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Ref No.</div>
                                <div class="col-md-9">{{ $customer->ref_no }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Card No.</div>
                                <div class="col-md-9">{{ $customer->card_no }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">AAN No.</div>
                                <div class="col-md-9">{{ $customer->aan_no }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Account No.</div>
                                <div class="col-md-9">{{ $customer->account_no }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Amount</div>
                                <div class="col-md-9">{{ $customer->amount }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Date</div>
                                <div class="col-md-9">{{ $customer->date_filing }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Reason</div>
                                <div class="col-md-9">{{ $customer->reason }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Address</div>
                                <div class="col-md-9">{{ $customer->address }}</div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">Notice Date</div>
                                <div class="col-md-9">{{ $customer->notice_date }}</div>
                              </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        @endforeach
                      </tbody>
                    </table>
                    
                  </div>
                  <!--  -->
                </div><!-- Pagination Links -->
                {{ $customers->links() }}
              </div>
              
            </div>
            
          </div>
          
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        
      <x-adminfooter />