   <div class="tab-pane active">
                        <div class="row">
                            <!-- System Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">System Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">PHP Version:</th>
                                                <td>{{ $phpVersion }}</td>
                                            </tr>
                                            <tr>
                                                <th>Laravel Version:</th>
                                                <td>{{ $laravelVersion }}</td>
                                            </tr>
                                            <tr>
                                                <th>MySQL Version:</th>
                                                <td>{{ $mysqlVersion }}</td>
                                            </tr>
                                            <tr>
                                                <th>Server Software:</th>
                                                <td>{{ $serverSoftware }}</td>
                                            </tr>
                                            <tr>
                                                <th>Operating System:</th>
                                                <td>{{ $serverOS }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- System Tools -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">System Tools</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-3">
                                            <div>
                                                <h5 class="mb-1">Clear Cache</h5>
                                                <p class="text-muted mb-0 small">Clear application, config, view, and route cache</p>
                                            </div>
                                            <button wire:click="clearCache" 
                                                    wire:confirm="Are you sure you want to clear all cache?"
                                                    class="btn btn-warning">
                                                <i class="fas fa-trash"></i> Clear Cache
                                            </button>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                            <div>
                                                <h5 class="mb-1">Optimize Application</h5>
                                                <p class="text-muted mb-0 small">Cache routes, config, and events for better performance</p>
                                            </div>
                                            <button wire:click="optimizeApplication" class="btn btn-success">
                                                <i class="fas fa-rocket"></i> Optimize
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>