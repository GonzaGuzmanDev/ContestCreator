<!DOCTYPE html>
<html lang="en">
<head>
    <title>OxoAwards</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="img/favicon.png"/>
    @yield('css')
    <style>
        .well{
            margin-bottom: 5px;
        }
        .well h4{
            margin: 0;
        }
        .well.entry{
            background: black;
        }
        .entry-img{
            margin-bottom: 5px;
        }
        .thumbs img{
            height: 100px;
            float: left;
            margin-right: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand"><img src="data:image/png;base64,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" alt=""/></a>
        </div>
    </div>
</nav>
<br>
<br>
<br>

@yield('content')

</body>
</html>