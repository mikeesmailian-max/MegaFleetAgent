<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$RESEND_API_KEY = 're_RfBRykVq_HCmoWiyWcD92MGcuXMDtvfPq';
$TO_EMAIL       = 'Mikee@Megafleetcorp.com';
$FROM_EMAIL     = 'info@megafleetcorp.com';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$first      = htmlspecialchars(trim($input['first']      ?? ''));
$last       = htmlspecialchars(trim($input['last']       ?? ''));
$email      = htmlspecialchars(trim($input['email']      ?? ''));
$phone      = htmlspecialchars(trim($input['phone']      ?? ''));
$experience = htmlspecialchars(trim($input['experience'] ?? ''));
$volume     = htmlspecialchars(trim($input['volume']     ?? ''));
$message    = htmlspecialchars(trim($input['message']    ?? ''));

if (!$first || !$last || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

$subject = "Freight Broker Application — {$first} {$last}";

$html_body = "
<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#0e0e0e;color:#f0eeeb;padding:32px;border-radius:8px;'>
  <div style='margin-bottom:24px;'>
    <span style='font-size:22px;font-weight:900;letter-spacing:2px;'>MEGA</span><span style='font-size:22px;font-weight:900;color:#C41230;letter-spacing:2px;'>FLEET</span><span style='font-size:12px;color:#888;margin-left:8px;'>CORP</span>
  </div>
  <h2 style='color:#C41230;margin:0 0 24px;font-size:18px;text-transform:uppercase;letter-spacing:1px;'>New Freight Broker Application</h2>
  <table style='width:100%;border-collapse:collapse;'>
    <tr><td style='padding:8px 0;color:#888;width:160px;font-size:13px;text-transform:uppercase;letter-spacing:1px;'>Name</td><td style='padding:8px 0;font-weight:600;'>{$first} {$last}</td></tr>
    <tr><td style='padding:8px 0;color:#888;font-size:13px;text-transform:uppercase;letter-spacing:1px;'>Email</td><td style='padding:8px 0;'><a href='mailto:{$email}' style='color:#C41230;'>{$email}</a></td></tr>
    <tr><td style='padding:8px 0;color:#888;font-size:13px;text-transform:uppercase;letter-spacing:1px;'>Phone</td><td style='padding:8px 0;'>{$phone}</td></tr>
    <tr><td style='padding:8px 0;color:#888;font-size:13px;text-transform:uppercase;letter-spacing:1px;'>Experience</td><td style='padding:8px 0;'>{$experience}</td></tr>
    <tr><td style='padding:8px 0;color:#888;font-size:13px;text-transform:uppercase;letter-spacing:1px;'>Monthly Volume</td><td style='padding:8px 0;'>{$volume}</td></tr>
  </table>
  " . ($message ? "<div style='margin-top:24px;padding:16px;background:#1c1c1c;border-left:3px solid #C41230;border-radius:4px;'><div style='color:#888;font-size:12px;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;'>Message</div><p style='margin:0;line-height:1.6;'>{$message}</p></div>" : "") . "
  <div style='margin-top:32px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.1);font-size:12px;color:#555;'>Submitted via megafleetcorp.com careers page</div>
</div>
";

$payload = json_encode([
    'from'    => "Mega Fleet Corp <{$FROM_EMAIL}>",
    'to'      => [$TO_EMAIL],
    'reply_to'=> $email,
    'subject' => $subject,
    'html'    => $html_body,
]);

$ch = curl_init('https://api.resend.com/emails');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $RESEND_API_KEY,
        'Content-Type: application/json',
    ],
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($http_code >= 200 && $http_code < 300) {
    echo json_encode(['success' => true, 'id' => $result['id'] ?? null]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $result['message'] ?? 'Failed to send email']);
}
?>
