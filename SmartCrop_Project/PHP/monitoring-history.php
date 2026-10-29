<?php

// Database connection
$host = "localhost";
$username = "root";
$password = "Pass@123";
$database = "smartcrop_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get all drone reports
$sql = "SELECT * FROM drone_reports ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Monitoring History - Smart Crop</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f8f5;
            color: #222;
        }

        .header {
            background: #166534;
            color: white;
            padding: 22px 35px;
        }

        .header h1 {
            font-size: 26px;
        }

        .header p {
            margin-top: 6px;
            opacity: 0.9;
        }

        .container {
            width: 95%;
            margin: 30px auto;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .top-section h2 {
            color: #166534;
        }

        .back-btn {
            text-decoration: none;
            background: #166534;
            color: white;
            padding: 10px 18px;
            border-radius: 7px;
        }

        .table-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th {
            background: #166534;
            color: white;
            padding: 13px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f0fdf4;
        }

        .good {
            color: #15803d;
            font-weight: bold;
        }

        .low {
            color: #15803d;
            font-weight: bold;
        }

        .date {
            white-space: nowrap;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }

    </style>

</head>

<body>

    <div class="header">

        <h1>🚁 Smart Crop - Monitoring History</h1>

        <p>Previous Drone Crop Monitoring Reports</p>

    </div>


    <div class="container">

        <div class="top-section">

            <h2>📊 Monitoring History</h2>

            <a href="drone-monitoring.html" class="back-btn">
                ← Back to Drone Monitoring
            </a>

        </div>


        <div class="table-box">

            <?php if ($result && $result->num_rows > 0) { ?>

                <table>

                    <thead>

                        <tr>

                            <th>Sr. No.</th>
                            <th>Farmer Name</th>
                            <th>Gut Number</th>
                            <th>Field</th>
                            <th>Crop</th>
                            <th>Crop Health</th>
                            <th>Water Stress</th>
                            <th>Pest Risk</th>
                            <th>Disease Risk</th>
                            <th>Dry Area</th>
                            <th>Date</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php

                        $sr = 1;

                        while ($row = $result->fetch_assoc()) {

                        ?>

                            <tr>

                                <td>
                                    <?php echo $sr++; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['farmer_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['gut_number']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['field_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['crop_name']); ?>
                                </td>

                                <td class="good">
                                    <?php echo htmlspecialchars($row['crop_health']); ?>
                                </td>

                                <td class="low">
                                    <?php echo htmlspecialchars($row['water_stress']); ?>
                                </td>

                                <td class="low">
                                    <?php echo htmlspecialchars($row['pest_risk']); ?>
                                </td>

                                <td class="low">
                                    <?php echo htmlspecialchars($row['disease_risk']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['dry_area']); ?>
                                </td>

                                <td class="date">
                                    <?php echo htmlspecialchars($row['created_at']); ?>
                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            <?php } else { ?>

                <div class="no-data">

                    <h3>📭 No Monitoring Reports Found</h3>

                    <p>
                        Upload and analyze a drone image to create your first report.
                    </p>

                </div>

            <?php } ?>

        </div>

    </div>

</body>

</html>

<?php

$conn->close();

?>