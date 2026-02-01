 <!doctype html>
 <html>

 <head>
     <meta charset="utf-8" />
     <title>Signing in…</title>
 </head>

 <body>
     <script>
         const targetOrigin = @json($origin);
         const payload = {
             type: "oauth:token",
             token: @json($tok)
         };

         try {
             if (
                 window.opener &&
                 !window.opener.closed &&
                 targetOrigin !== '*'
             ) {
                 window.opener.postMessage(payload, targetOrigin);
             }
         } finally {
             window.close();
         }
     </script>
 </body>

 </html>
