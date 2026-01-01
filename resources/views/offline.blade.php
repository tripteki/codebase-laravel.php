<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ __("common.offline_title") }} - {{ \Illuminate\Support\Str::headline(config("app.name")) }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        p {
            font-size: 18px;
            margin-bottom: 32px;
            opacity: 0.9;
            line-height: 1.6;
        }

        button {
            background: #fff;
            color: #667eea;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        button:active {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">📡</div>
        <h1>{{ __("common.offline_title") }}</h1>
        <p>{{ __("common.offline_message") }}</p>
        <button onclick="window.location.reload()">{{ __("common.retry") ?? "Retry" }}</button>
    </div>
</body>

</html>