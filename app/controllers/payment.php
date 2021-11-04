<html>
    <?php
    include("../../path.php"); 
    include("app/database/connect.php"); 
    // include (ROOT_PATH . '/register_event.php');

     // For test payments we want to enable the sandbox mode. If you want to put live
// payments through then this setting needs changing to `false`.
$enableSandbox = true;

// Database settings. Change these for your database configuration.
// $dbConfig = [
//     'host' => 'localhost',
//     'username' => 'root',
//     'password' => '',
//     'name' => 'blog'
// ];

// PayPal settings. Change these to your account details and the relevant URLs
// for your site.
$paypalConfig = [
    'email' => $_POST['payer_email'],
    'return_url' => 'http://esportsbn.com/index.php',
    'cancel_url' => 'http://esportsbn.com/event_register.php',
    'notify_url' => 'http://esportsbn.com/payments.php'
];

$paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

// Product being purchased.
$itemName = 'Registeration for Tournament';
$amount = $_POST['amountSent'];

// Include Functions
require ROOT_PATH . "/app/database/db.php";

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {

    // Grab the post data so that we can set up the query string for PayPal.
    // Ideally we'd use a whitelist here to check nothing is being injected into
    // our post data.
    $data = [];
    foreach ($_POST as $key => $value) {
        $data[$key] = stripslashes($value);
    }

    // Set the PayPal account.
    $data['business'] = $paypalConfig['email'];

    // Set the PayPal return addresses.
    $data['return'] = stripslashes($paypalConfig['return_url']);
    $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
    $data['notify_url'] = stripslashes($paypalConfig['notify_url']);

    // Set the details about the product being purchased, including the amount
    // and currency so that these aren't overridden by the form data.
    $data['item_name'] = $itemName;
    $data['amountSent'] = $amount;
    $data['currency_code'] = 'SGD';

    // Add any custom fields for the query string.
    //$data['custom'] = USERID;

    // Build the query string from the data.
    $queryString = http_build_query($data);

    // Redirect to paypal IPN
    header('location:' . $paypalUrl . '?' . $queryString);
    exit();

} else {
    // Handle the PayPal response.
    
// // Create a connection to the database.
// $db = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['name']);

// $sql = 'CREATE TABLE IF NOT EXISTS `payments` (
//     `id` int(6) NOT NULL AUTO_INCREMENT,
//     `txnid` varchar(20) NOT NULL,
//     `payment_amount` decimal(7,2) NOT NULL,
//     `payment_status` varchar(25) NOT NULL,
//     `itemid` varchar(25) NOT NULL,
//     `createdtime` datetime NOT NULL,
//     PRIMARY KEY (`id`)
//     ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1)';

// Assign posted variables to local data array.
$data = [
    'item_name' => $_POST['item_name'],
    'payment_status' => $_POST['payment_status'],
    'payment_amount' => $_POST['mc_gross'],
    'payment_currency' => $_POST['currency_code'],
    'txn_id' => $_POST['txn_id'],
    'receiver_email' => $_POST['receiver_email'],
    'payer_email' => $_POST['payer_email'],
    //use the esports dummy email for this later ^
];

// We need to verify the transaction comes from PayPal and check we've not
// already processed the transaction before adding the payment to our
// database.
if (verifyTransaction($_POST) && checkTxnid($data['txn_id'])) {
    if (addPayment($data) !== false) {
        // Payment successfully added.
    }
}
}
?>
</html>