<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            border-top: 5px solid #d63384;
            /* Pink accent color */
            padding: 40px 20px;
        }

        h2 {
            color: #333;
            margin-top: 0;
        }

        p {
            line-height: 1.6;
            color: #555;
            font-size: 16px;
        }

        .signature {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 14px;
            color: #777;
        }

        .signature strong {
            color: #333;
            font-size: 16px;
        }

        .contact-info {
            margin-top: 5px;
        }

        .social-links {
            margin-top: 15px;
        }

        .social-links a {
            margin-right: 10px;
            color: #d63384;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Hello {{ $submission->name }},</h2>
        <p>Thank you for reaching out to us! We have received your inquiry regarding
            @if($submission->listing_key) <strong>Listing #{{ $submission->listing_key }}</strong> @endif
            @if($submission->city) in <strong>{{ $submission->city }}</strong>@endif.
        </p>
        <p>One of our team members will review your message and get back to you as soon as possible.</p>
        <p>If your matter is urgent, please feel free to call us directly.</p>

        <div class="signature">
            <strong>May Lee Realtor Team</strong><br>
            <div class="contact-info">
                1550 16th Ave, Suite: 3 & 4, Richmond Hill, Ontario<br>
                Phone: (001) - 647 885 0114<br>
                Email: info@mayleerealtor.com
            </div>
        </div>
    </div>
</body>

</html>