<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management App</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" href="/icons/icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json?v=3">
    <meta name="theme-color" content="#0d6efd">

</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Pentagone Stock System</h1>
        
        <div class="row">
           <!-- Add New Product Form -->
<div class="col-md-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Add Product
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.products.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="productName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="productName" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="initialStock" class="form-label">Initial Stock</label>
                    <input type="number" step="0.01" class="form-control" id="initialStock" name="stock" required>
                </div>

                <div class="mb-3">
                    <label for="unitCost" class="form-label">Unit Cost Price (Ksh)</label>
                    <input type="number" step="0.01" class="form-control" id="unitCost" name="cost_price" required>
                </div>

                <div class="mb-3">
                    <label for="unitPrice" class="form-label">Unit Selling Price (Ksh)</label>
                    <input type="number" step="0.01" class="form-control" id="unitPrice" name="selling_price" required>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" id="cancelEdit" class="btn btn-outline-secondary no-print" style="display: none;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

            
            <!-- Record Purchases -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        Record Purchases
                    </div>
                    <div class="card-body">
                        <form id="purchaseForm">
                            <div class="mb-3">
                                <label for="purchaseProduct" class="form-label">Product</label>
                                <select class="form-select" id="purchaseProduct" required>
                                    <option value="">Select a product</option>
                                    <!-- Products will be added here by JavaScript -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="purchaseQuantity" class="form-label">Quantity Purchased</label>
                                <input type="number" class="form-control" id="purchaseQuantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="purchaseUnitCost" class="form-label">Unit Cost Price (Ksh)</label>
                                <input type="number" step="0.01" class="form-control" id="purchaseUnitCost" required>
                            </div>
                            <button type="submit" class="btn btn-info">Record Purchase</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Record Sales -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        Record Sales
                    </div>
                    <div class="card-body">
                        <form id="salesForm">
                            <div class="mb-3">
                                <label for="salesProduct" class="form-label">Product</label>
                                <select class="form-select" id="salesProduct" required>
                                    <option value="">Select a product</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quantitySold" class="form-label">Quantity Sold</label>
                                <input type="number" step="0.01" class="form-control" id="quantitySold" required>
                            </div>
                            <button type="submit" class="btn btn-success">Record Sale</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reports Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        Reports
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Report Type</label>
                                    <select class="form-select" id="reportType">
                                        <option value="sales">Sales Report</option>
                                        <option value="purchases">Purchases Report</option>
                                        <option value="inventory">Inventory Report</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="generateReport" class="btn btn-secondary">Generate Report</button>
                            </div>
                        </div>
                        <div id="reportResults" class="mt-3">
                            <p>Select report type and date range to view data.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button id="exportData" class="btn btn-dark no-print">Export Data</button>
        <button id="importData" class="btn btn-dark no-print">Import Data</button>
        <input type="file" id="importFile" accept=".json" style="display: none;">
        <!-- Product Inventory Table -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                Product Inventory
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Previous Stock</th>
                                <th>Purchased</th>
                                <th>Sold Today</th>
                                <th>Current Stock</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Profit per Unit</th>
                                <th>Total Profit Today</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productTable">
                            <!-- Products will be listed here -->
                        </tbody>
                    </table>
                </div>
                <footer>
                    <div class="cprt">
                        <p style="text-align: center; color: blue; font-style: italic;">James IT Place || © Copyrights 2025</p>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="editProductName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editCurrentStock" class="form-label">Current Stock</label>
                            <input type="number" class="form-control" id="editCurrentStock" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUnitCost" class="form-label">Unit Cost Price (Ksh)</label>
                            <input type="number" step="0.01" class="form-control" id="editUnitCost" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUnitPrice" class="form-label">Unit Selling Price (Ksh)</label>
                            <input type="number" step="0.01" class="form-control" id="editUnitPrice" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>
    </div>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/service-worker.js')
        .then(registration => {
          console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch(error => {
          console.error('Service Worker registration failed:', error);
        });
    });
  }
</script>

</body>
</html>