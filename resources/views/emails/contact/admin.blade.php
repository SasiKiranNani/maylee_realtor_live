<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Submission</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #4c418c;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        .info-row {
            margin-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 10px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .value {
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }

        .message-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #4c418c;
            margin-top: 10px;
        }

        .footer {
            background-color: #f4f6f9;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: #e2e6ea;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>New Lead: {{ ucfirst(str_replace('_', ' ', $submission->source)) }}</h1>
        </div>
        <div class="content">
            <div class="info-row">
                <span class="label">Name</span>
                <div class="value">{{ $submission->name }}</div>
            </div>

            <div class="info-row">
                <span class="label">Email</span>
                <div class="value"><a href="mailto:{{ $submission->email }}"
                        style="color: #4c418c; text-decoration: none;">{{ $submission->email }}</a></div>
            </div>

            @if($submission->phone)
                <div class="info-row">
                    <span class="label">Phone</span>
                    <div class="value"><a href="tel:{{ $submission->phone }}"
                            style="color: #4c418c; text-decoration: none;">{{ $submission->phone }}</a></div>
                </div>
            @endif

            @if($submission->city)
                <div class="info-row">
                    <span class="label">Interested City</span>
                    <div class="value">{{ $submission->city }}</div>
                </div>
            @endif

            @if($submission->listing_key)
                <div class="info-row">
                    <span class="label">Listing Key</span>
                    <div class="value"><span class="badge">{{ $submission->listing_key }}</span></div>
                </div>
            @endif

            <div class="info-row">
                <span class="label">Message</span>
                <div class="value message-box">
                    {{ $submission->message ?? 'No message provided.' }}
                </div>
            </div>

            <div class="info-row">
                <span class="label">Source Page</span>
                <div class="value">{{ ucfirst(str_replace('_', ' ', $submission->source)) }}</div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} May Lee Realtor. All rights reserved.
        </div>
    </div>
</body>

</html>