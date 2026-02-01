 <!doctype html>
 <html>

 <head>
     <meta charset="utf-8" />
     <title>Signing in…</title>
 </head>

 <body>
     <script>
         (function() {
             const targetOrigin = "{{ $origin }}";
             const payload = {
                 type: "oauth:token",
                 token: "{{ $tok }}"
             };

             try {
                 if (window.opener && !window.opener.closed) {
                     window.opener.postMessage(payload, targetOrigin);
                 }
             } finally {
                 window.close();
             }
         })();
     </script>
 </body>

 </html>
