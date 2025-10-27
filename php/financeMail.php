<?php
header('Content-Type: application/json');
error_reporting(0);

// Replace with your admin email
$admin1 = "sumitkumarsahu141@gmail.com";  // 🟢 change this
$admin2 = "Ajain0237@gmail.com";
$admin3 = "mjfinserv2000@zohomail.in";

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Sanitize form fields
function clean($data) {
    return htmlspecialchars(trim($data));
}

$customerName   = clean($_POST['customerName'] ?? '');
$mobileNumber   = clean($_POST['mobileNumber'] ?? '');
$brand          = clean($_POST['brand'] ?? '');
$productValue   = clean($_POST['productValue'] ?? '');
$downPayment    = clean($_POST['downPayment'] ?? '');
$financeAmount  = clean($_POST['financeAmount'] ?? '');
$financeCharges = clean($_POST['financeCharges'] ?? '');
$emiMonths      = clean($_POST['emiMonths'] ?? '');
$financeCompany = clean($_POST['financeCompany'] ?? '');
$promoterName   = clean($_POST['promoterName'] ?? '');
$promoterMobile = clean($_POST['promoterMobile'] ?? '');
$granterName    = clean($_POST['granterName'] ?? '');
$granterNumber  = clean($_POST['granterNumber'] ?? '');
$firstEmiDate   = clean($_POST['firstEmiDate'] ?? '');
$address        = clean($_POST['address'] ?? '');
$landmark       = clean($_POST['landmark'] ?? '');
$officeName     = clean($_POST['officeName'] ?? '');
$officeAddress  = clean($_POST['officeAddress'] ?? '');

// CSV file path (change if needed, ensure writable permissions)
$csvFile = 'finance_applications.csv';

// Prepare data array for CSV
$data = [
    $customerName,
    $mobileNumber,
    $brand,
    $productValue,
    $downPayment,
    $financeAmount,
    $financeCharges,
    $emiMonths,
    $financeCompany,
    $promoterName,
    $promoterMobile,
    $granterName,
    $granterNumber,
    $firstEmiDate,
    $address,
    $landmark,
    $officeName,
    $officeAddress
];

// Write to CSV
if (!file_exists($csvFile)) {
    // Create file with headers if it doesn't exist
    $headers = [
        "Customer Name",
        "Mobile Number",
        "Brand",
        "Product Value",
        "Down Payment",
        "Finance Amount",
        "Finance Charges",
        "EMI Months",
        "Finance Company",
        "Promoter Name",
        "Promoter Mobile",
        "Granter Name",
        "Granter Number",
        "1st EMI Date",
        "Address",
        "Landmark",
        "Office Name",
        "Office Address"
    ];
    $fp = fopen($csvFile, 'w');
    fputcsv($fp, $headers);
    fclose($fp);
}

// Append the new data row
$fp = fopen($csvFile, 'a');
fputcsv($fp, $data);
fclose($fp);

// Prepare email content
$subject = "New Finance Application from $customerName";

$htmlMessage = "
<h2>New Finance Application Details</h2>
<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
<tr><td><strong>Customer Name:</strong></td><td>$customerName</td></tr>
<tr><td><strong>Mobile Number:</strong></td><td>$mobileNumber</td></tr>
<tr><td><strong>Brand:</strong></td><td>$brand</td></tr>
<tr><td><strong>Product Value:</strong></td><td>$productValue</td></tr>
<tr><td><strong>Down Payment:</strong></td><td>$downPayment</td></tr>
<tr><td><strong>Finance Amount:</strong></td><td>$financeAmount</td></tr>
<tr><td><strong>Finance Charges:</strong></td><td>$financeCharges</td></tr>
<tr><td><strong>EMI Months:</strong></td><td>$emiMonths</td></tr>
<tr><td><strong>Finance Company:</strong></td><td>$financeCompany</td></tr>
<tr><td><strong>Promoter Name:</strong></td><td>$promoterName</td></tr>
<tr><td><strong>Promoter Mobile:</strong></td><td>$promoterMobile</td></tr>
<tr><td><strong>Granter Name:</strong></td><td>$granterName</td></tr>
<tr><td><strong>Granter Number:</strong></td><td>$granterNumber</td></tr>
<tr><td><strong>1st EMI Date:</strong></td><td>$firstEmiDate</td></tr>
<tr><td><strong>Address:</strong></td><td>$address</td></tr>
<tr><td><strong>Landmark:</strong></td><td>$landmark</td></tr>
<tr><td><strong>Office Name:</strong></td><td>$officeName</td></tr>
<tr><td><strong>Office Address:</strong></td><td>$officeAddress</td></tr>
</table>
";

// Function to send email with attachment
function sendEmailWithAttachment($to, $subject, $htmlMessage, $csvFile) {
    $boundary = md5(time());

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"" . "\r\n";
    $headers .= "From: MJ Finance Website <no-reply@yourdomain.com>" . "\r\n";

    $message = "--$boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $htmlMessage . "\r\n";

    // Attachment
    $fileContent = file_get_contents($csvFile);
    $fileContent = chunk_split(base64_encode($fileContent));

    $message .= "--$boundary\r\n";
    $message .= "Content-Type: text/csv; name=\"" . basename($csvFile) . "\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"" . basename($csvFile) . "\"\r\n\r\n";
    $message .= $fileContent . "\r\n";
    $message .= "--$boundary--\r\n";

    return mail($to, $subject, $message, $headers);
}

// Send emails to all admins
$success = true;

if (!sendEmailWithAttachment($admin1, $subject, $htmlMessage, $csvFile)) {
    $success = false;
}
if (!sendEmailWithAttachment($admin2, $subject, $htmlMessage, $csvFile)) {
    $success = false;
}
if (!sendEmailWithAttachment($admin3, $subject, $htmlMessage, $csvFile)) {
    $success = false;
}

if ($success) {
    echo json_encode(['status' => 'success', 'message' => '✅ Thank you! Your application has been submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => '❌ Failed to send email. Please try again later.']);
}
?>