<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS | Installation</title>

    <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        .background {
            padding: 50px 0;
        }

        .section-setup h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #333;
        }

        .progressbar {
            display: flex;
            justify-content: space-between;
            counter-reset: step;
            margin-bottom: 40px;
            padding-left: 0;
            list-style: none;
        }

        .progressbar li {
            position: relative;
            text-align: center;
            flex: 1;
            color: #999;
        }

        .progressbar li::before {
            content: counter(step);
            counter-increment: step;
            width: 35px;
            height: 35px;
            line-height: 35px;
            border: 2px solid #ddd;
            display: block;
            text-align: center;
            margin: 0 auto 10px auto;
            border-radius: 50%;
            background-color: #fff;
        }

        .progressbar li::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: #ddd;
            top: 18px;
            left: -50%;
            z-index: -1;
        }

        .progressbar li:first-child::after {
            content: none;
        }

        .progressbar li.active {
            color: #0d6efd;
        }

        .progressbar li.active::before {
            border-color: #0d6efd;
            background-color: #0d6efd;
            color: #fff;
        }

        .progressbar li.active::after {
            background-color: #0d6efd;
        }

        /* Form styling */
        .form-control, .form-select {
            border-radius: 0.35rem;
        }

        .btn {
            border-radius: 0.35rem;
        }

        .container-progress {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }

        .section-setup {
            margin-bottom: 30px;
        }

        .list-group-item {
            font-weight: 500;
        }

        .loader {
            display: none;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<div class="background">
    <div class="container-progress container">
        <div class="row text-center section-setup">
            <div class="col-12">
                <h1>POS Installation</h1>
            </div>
        </div>

        @yield('content')
    </div>
</div>


</body>
</html>