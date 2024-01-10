<?php

require_once '../header.php';
require_once '../../db_config.php';

// Initialize variables
$success = $error = '';

// Establish a database connection
$mysqli = new mysqli('localhost', 'root', '', 'tms');

// Check if the connection is successful
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Check if form is submitted
if (isset($_POST['save_receive'])) {
    // Check if the required keys are set in the $_POST array
    if (
        isset($_POST['purchaseOrderId']) &&
        isset($_POST['productName']) &&
        isset($_POST['supplierName']) &&
        isset($_POST['ReceivedQuantity']) &&
        isset($_POST['ReceiveDate'])
    ) {
        // Retrieve data from the form
        $purchaseOrderId = $_POST['purchaseOrderId'];
        $productName = $_POST['productName'];
        $supplierName = $_POST['supplierName'];
        $receivedQuantity = $_POST['ReceivedQuantity'];
        $receiveDate = $_POST['ReceiveDate'];

        // Perform database lookup to get ProductID, SupplierID, and WeightID
        $productQuery = "SELECT ProductID FROM productmanagement WHERE ProductName = '$productName'";
        $productResult = $mysqli->query($productQuery);

        if ($productResult && $productResult->num_rows > 0) {
            $productRow = $productResult->fetch_assoc();
            $productId = $productRow['ProductID'];
        } else {
            $error = "Product not found in the database.";
            // Handle the error as needed
        }

        $supplierQuery = "SELECT SupplierID FROM suppliers WHERE SupplierName = '$supplierName'";
        $supplierResult = $mysqli->query($supplierQuery);

        if ($supplierResult && $supplierResult->num_rows > 0) {
            $supplierRow = $supplierResult->fetch_assoc();
            $supplierId = $supplierRow['SupplierID'];
        } else {
            $error = "Supplier not found in the database.";
            // Handle the error
        }

        // Assuming WeightName is directly used in the form, you can modify this as needed
        $weightName = $_POST['WeightName'];
        $weightQuery = "SELECT WeightID FROM weightunitmanagement WHERE WeightName = '$weightName'";
        $weightResult = $mysqli->query($weightQuery);

        if ($weightResult && $weightResult->num_rows > 0) {
            $weightRow = $weightResult->fetch_assoc();
            $weightId = $weightRow['WeightID'];
        } else {
            $error = "Weight not found in the database.";
            // Handle the error
        }

        // Validate the data if needed

        // Insert data into yarnreceivemanagement table
        $insertQuery = "INSERT INTO yarnreceivemanagement (PurchaseOrderID, ProductID, SupplierID, ReceivedQuantity, WeightID, ReceiveDate)
                        VALUES ('$purchaseOrderId', '$productId', '$supplierId', '$receivedQuantity', '$weightId', '$receiveDate')";

        $result = $mysqli->query($insertQuery);

        if ($result) {
            $success = "Data inserted successfully!";
        } else {
            $error = "Error: " . $mysqli->error;
        }
    } else {
        $error = "One or more required fields are missing in the form submission.";
    }
}

?>

<!-- Content for yarnreceivemanagement.php -->
<div class="content">
    <!-- content HEADER -->
    <div class="content-header">
        <div class="leftside-content-header">
            <ul class="breadcrumbs">
                <li><i class="fa fa-home" aria-hidden="true"></i><a href="index.php">Dashboard</a></li>
                <li><i class="fa fa-tasks" aria-hidden="true"></i><a href="javascript:avoid(0)">Yarn Receive
                        Management</a></li>
            </ul>
        </div>
    </div>

    <div class="row animated fadeInUp">
        <div class="col-sm-6 col-sm-offset-3">
            <!-- Display success or error message -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <h4 class="section-subtitle"><b>Yarn Receive</b></h4>
            <div class="panel">
                <div class="panel-content">
                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

                                <!-- PurchaseOrderID Dropdown -->
                                <div class="form-group">
                                    <label for="PurchaseOrderID" class="col-sm-4 control-label">Purchase Order:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="purchaseOrderId" name="purchaseOrderId"
                                            onchange="loadProductAndSupplierNames()">
                                            <option>Select One</option>
                                            <!-- Options will be populated dynamically using JavaScript -->
                                        </select>
                                    </div>
                                </div>

                                <!-- ProductID Dropdown -->
                                <div class="form-group">
                                    <label for="ProductID" class="col-sm-4 control-label">Product:</label>
                                    <div class="col-sm-8">
                                        <input type="text"  class="form-control" id="productName" name="productName" readonly>
                                    </div>
                                </div>

                                <!-- SupplierID Dropdown -->
                                <div class="form-group">
                                    <label for="SupplierID" class="col-sm-4 control-label">Supplier:</label>
                                    <div class="col-sm-8">
                                        <input type="text"  class="form-control" id="supplierName" name="supplierName" readonly>
                                    </div>
                                </div>

                                <!-- PurchaseOrderQuantity Input -->
                                <div class="form-group">
                                    <label for="PurchaseOrderQuantity" class="col-sm-4 control-label">Purchase Order
                                        Quantity:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="PurchaseOrderQuantity"
                                            name="PurchaseOrderQuantity" readonly>
                                    </div>
                                </div>
                                <!-- RemainingReceiveQuantity Input (display-only) -->
                                <div class="form-group">
                                    <label for="RemainingReceiveQuantity" class="col-sm-4 control-label">Remaining
                                        Receive Quantity:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="RemainingReceiveQuantity"
                                            name="RemainingReceiveQuantity" readonly>
                                    </div>
                                </div>

                                <!-- TotalReceivedQuantity Input (display-only) -->
                                <div class="form-group">
                                    <label for="TotalReceivedQuantity" class="col-sm-4 control-label">Total Received
                                        Quantity:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="TotalReceivedQuantity"
                                            name="TotalReceivedQuantity" readonly>
                                    </div>
                                </div>

                                <!-- ReceivedQuantity Input -->
                                <div class="form-group">
                                    <label for="ReceivedQuantity" class="col-sm-4 control-label">Received
                                        Quantity:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="ReceivedQuantity"
                                            name="ReceivedQuantity" placeholder="Enter received quantity">
                                    </div>
                                </div>

                                <!-- WeightID Dropdown -->
                                <div class="form-group">
                                    <label for="WeightID" class="col-sm-4 control-label">Weight:</label>
                                    <div class="col-sm-8">
                                        <input type="text"  class="form-control" id="WeightName" name="WeightName" readonly>
                                    </div>
                                </div>

                                <!-- ReceiveDate Input -->
                                <div class="form-group">
                                    <label for="ReceiveDate" class="col-sm-4 control-label">Receive Date:</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="ReceiveDate" name="ReceiveDate">
                                    </div>
                                </div>
                                <!-- TotalReceivedQuantity Input (display-only) -->
<div class="form-group">
    <label for="TotalReceivedQuantity" class="col-sm-4 control-label">Total Received Quantity:</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" id="TotalReceivedQuantity" name="TotalReceivedQuantity" readonly>
    </div>
</div>

<!-- RemainingReceiveQuantity Input (display-only) -->
<div class="form-group">
    <label for="RemainingReceiveQuantity" class="col-sm-4 control-label">Remaining Receive Quantity:</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" id="RemainingReceiveQuantity" name="RemainingReceiveQuantity" readonly>
    </div>
</div>


                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <button type="submit" class="btn btn-primary" name="save_receive"><i
                                                class="fa fa-save"></i> Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="scripts.js"></script>

<?php
// Include footer
require_once '../footer.php';
?>
