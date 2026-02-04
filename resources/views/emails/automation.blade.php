<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Automation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #11101d; color: #fff; padding: 16px 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 24px; border: 1px solid #e9ecef; border-top: none; border-radius: 0 0 8px 8px; }
        .field { margin-bottom: 16px; }
        .label { font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .value { margin-top: 4px; }
        .message-box { background: #fff; border-left: 4px solid #11101d; padding: 12px 16px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <strong>New message from Email Automation</strong>
    </div>
    <div class="content">
        <div class="field">
            <div class="label">Name</div>
            <div class="value">{{ $firstName }} {{ $lastName }}</div>
        </div>
        <div class="field">
            <div class="label">Email</div>
            <div class="value"><a href="mailto:{{ $email }}">{{ $email }}</a></div>
        </div>
        <div class="field">
            <div class="label">Message</div>
            <div class="message-box">{{ $messageContent }}</div>
        </div>
    </div>
</body>
</html>
