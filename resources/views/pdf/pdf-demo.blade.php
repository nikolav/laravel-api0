<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        {!! App\Helpers\AppUtils::vite_resource() !!}
    </style>
    <title>Document</title>
</head>

<body class="font-sans bg-lime-400">
    <section class="text-slate-800 text-center">
        <h1 class="text-2xl">pdf:demo</h1>
        <p>
            Hello World!
        </p>
        <small>
            <pre>[{{ now() }}]</pre>
        </small>
    </section>
</body>

</html>
