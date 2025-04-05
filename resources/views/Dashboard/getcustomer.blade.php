<x-adminheader />
<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">List customers </h4>
                  <!--  -->
                  <ul class="navbar-nav mr-lg-2">
                  <li class="nav-item nav-search d-none d-lg-block">
                    <div class="input-group">
                      <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                        <span class="input-group-text" id="search">
                          <i class="icon-search"></i>
                        </span>
                      </div>
                      <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
                    </div>
                  </li>
                </ul>
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
                        </tr>
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