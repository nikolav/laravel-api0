<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Signing in…</title>
</head>

<body>
    <script>
        (function() {
            const targetOrigin = @json($origin);
            const payload = @json($tok ? ['type' => 'oauth:token', 'token' => $tok] : ['type' => 'oauth:error', 'error' => 'missing_token']);

            try {
                // basic sanity check
                if (typeof targetOrigin !== 'string' || targetOrigin === '*') return;

                if (window.opener && !window.opener.closed) {
                    window.opener.postMessage(payload, targetOrigin);
                    setTimeout(() => window.close(), 0);
                    return;
                }
            } finally {
                // ensure popup closes even if opener is missing
                setTimeout(() => window.close(), 0);
            }
        })();
    </script>
</body>

</html>
